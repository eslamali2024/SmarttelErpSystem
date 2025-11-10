<?php

namespace App\Traits\Approval;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Hr\App\Models\Employee;
use App\Models\Approval\ApprovalFlow;
use App\Events\Approval\CancelApproval;
use App\Events\Approval\RejectApproval;
use App\Enums\Approval\ApprovalTypeEnum;
use App\Models\Approval\ApprovalRequest;
use App\Models\Approval\ApprovalFlowStep;
use App\Enums\Approval\ApprovalStatusEnum;
use App\Events\Approval\ApprovalCompleted;
use App\Events\Approval\CreateNewApproval;
use App\Events\Approval\FirstApprovalCompleted;
use App\Events\Approval\CancelApprovalCompleted;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Hr\App\Models\Organization\Department;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
    * This trait provides functionality for models that can be approved.
    * It includes methods to handle approval flows, requests, and steps.
    * It automatically creates an approval request when the model is created,
    * and manages the approval process through various methods.
    * It also includes caching mechanisms to reduce database queries for approval flow steps,
    * employee retrieval, and approval request actions.

    * this trait is intended to be used in models that require an approval process,
    * such as leave requests, expense claims, or any other request that needs approval.
    * It provides methods to retrieve approval flow steps, approval request actions,
    * and to determine if the current user can approve or cancel an approval request.
    * The trait also includes methods to check if the current user is authorized to approve a step
    * based on their role, permissions, manager sector, division, department, section, department request, department approval Or User...

    * Created by: Thomas Emad
    * Position: Junior Software Developer
    * Company: Smarttel
    * Started at: 2025-07-17 (Thursday)
    * Finished at: 2025-07-20 (Sunday)
    * Notes:
    *       - If you find messy or bad code, it might not be mine — or it could be from a time when I was still learning.
    *           In either case, I apologize and appreciate your patience.
    *       - If you find clean and good code, then thank you — the credit is yours for recognizing it.
*/

trait Approvable
{
    /* @var ?Collection<ApprovalFlowStep> */
    protected $cachedApprovalFlowSteps = null;

    /* @var ?Employee */
    protected $cachedEmployee = null;

    /* @var ?Collection<ApprovalRequestAction> */
    protected $cachedApprovalRequestSteps = null;

    /**
     * Automatically create an approval request when an approvable model is created
     *
     * If the model has an approval flow, create an approval request for it after the model is saved.
     *
     * @return void
     */
    protected static function bootApprovable(): void
    {
        static::created(function ($model) {
            $flow = $model->getApprovalFlow();
            if ($flow) {
                $approvalRequest =    $model->approvalRequest()?->create([
                    'approval_flow_id'  => $flow->id,
                    'approvable_type'   => get_class($model),
                    'approvable_id'     => $model->id,
                    'creator_id'        => auth()->id(),
                    'status'            => ApprovalStatusEnum::PENDING->value
                ]);
                if ($approvalRequest) {
                    event(new CreateNewApproval($model, $approvalRequest, auth()->user()));
                }
            }
        });
        static::deleted(function ($model) {
            $model->approvalRequest()?->delete();
        });
    }

    /**
     * Get the approval flow for this model
     *
     * @return ?ApprovalFlow
     */
    public function getApprovalFlow(): ?ApprovalFlow
    {
        return ApprovalFlow::query()
            ->where('approvable_type', static::class)
            ->first();
    }

    /**
     * Get the approval request for this model
     *
     * @return HasOne
     */
    public function approvalRequest(): HasOne
    {
        return $this->hasOne(ApprovalRequest::class, 'approvable_id')
            ->where('approvable_type', static::class);
    }

    /**
     * Get the approval flow steps for this model
     *
     * @return HasMany
     */
    public function approvalFlowSteps(): ?HasMany
    {
        return $this->hasMany(ApprovalFlowStep::class)?->orderBy('order');
    }

    /**
     * Retrieve the approval flow steps for this model
     *
     * @return ?Collection<ApprovalFlowStep>
     */
    public function getApprovalFlowSteps()
    {
        if (is_null($this->cachedApprovalFlowSteps)) {
            $this->cachedApprovalFlowSteps = $this->getApprovalFlow()?->approvalFlowSteps;
        }
        return $this->cachedApprovalFlowSteps;
    }

    /**
     * Retrieve an employee by their ID.
     * Results are cached to reduce queries.
     *
     * @param int $id
     * @return Employee|null
     */
    public function getEmployee($id)
    {
        if (is_null($this->cachedEmployee)) {
            $this->cachedEmployee = Employee::with('current_company_position')->find($id);
        }
        return $this->cachedEmployee;
    }

    /**
     * Get the approval request steps for this model
     *
     * @return HasMany
     */
    public function approvalRequestSteps(): HasMany
    {
        return $this->approvalRequest->approvalRequestActions();
    }

    /**
     * Retrieve the approval request actions for this approvable model.
     *
     * @return HasMany|null
     */
    public function getApprovalRequestSteps($loadApprover = false)
    {
        if (is_null($this->cachedApprovalRequestSteps)) {
            $actions = $this->approvalRequest?->approvalRequestActions;
            if ($loadApprover) {
                $this->cachedApprovalRequestSteps = $this->loadUsersForActions($actions);
            }
            $this->cachedApprovalRequestSteps = $actions;
        }
        return $this->cachedApprovalRequestSteps;
    }

    /**
     * Retrieve the approval steps for this model, along with the approval request action associated to each step.
     *
     * @return Collection<array{step: ProcessApprovalFlowStep, action: ApprovalRequestAction}>
     */
    public function getApprovalStepsWithActions($steps = [], $actions = [], $request = null, $loadApprover = false): Collection
    {
        $steps   = $steps ?: $this->getApprovalFlowSteps();
        $actions = $actions ?: $this->getApprovalRequestSteps($loadApprover);
        $request = $request ?: $this->approvalRequest;

        if ($steps && $request) {
            $actionsByStepId = $actions?->keyBy('approval_flow_step_id');

            $items = $steps->map(function ($step) use ($actionsByStepId) {
                return [
                    'step'    => $step,
                    'action'  => $actionsByStepId?->get($step->id),
                ];
            });

            return collect(['items' => $items])->put('count', $this->getCountSteps($items));
        }
        return collect(['items' => collect([])])->put('count', $this->getCountSteps());
    }

    /**
     * Loads the approver user for each action in the given collection.
     *
     * @param Collection<ApprovalRequestAction> $actions
     * @return Collection<ApprovalRequestAction>
     */
    private function loadUsersForActions($actions)
    {
        $approverIds = $actions?->pluck('approver_id')->filter()->unique();
        if ($approverIds) {

            if ($approverIds->isEmpty()) {
                return $actions;
            }

            $approvers = User::with('roles')->whereIn('id', $approverIds)
                ->get()
                ->keyBy('id');

            return $actions->each(function ($action) use ($approvers) {
                $action->approver = $approvers[$action->approver_id] ?? null;
            });
        }
        return $actions;
    }

    /**
     * Retrieve the count of steps for this model.
     *
     * @param array<array{step: ProcessApprovalFlowStep, action: ApprovalRequestAction}> $items
     * @return array{
     *     completed: int,
     *     uncompleted: int,
     *     current: int
     * }
     */
    public function getCountSteps($items = []): array
    {
        $countSteps = [
            'completed'     => 0,
            'uncompleted'   => 0,
            'current'       => 0
        ];

        foreach ($items as $step) {
            if (!$step['action']) {
                $countSteps['uncompleted']++;
            } else {
                $countSteps['completed']++;
                $countSteps['current']++;
            }
        }

        return $countSteps;
    }

    /**
     * Get the next step along with the associated request.
     *
     * Iterates through approval flow steps to find the first step that has not been completed.
     * If a step with a rejected action type is found, returns null for the step and the associated approval request.
     * Otherwise, returns the first uncompleted step, the associated approval request, and step counts.
     *
     * @return array{
     *     step: ?ApprovalFlowStep,
     *     request: ApprovalRequest,
     *     countSteps?: array{
     *         completed: int,
     *         uncompleted: int,
     *         current: int
     *     }
     * }|null
     */
    public function nextStepWithRequest($loadApprover = false): array|null
    {
        $steps          = $this->getApprovalFlowSteps();
        $requestSteps   = $this->getApprovalRequestSteps($loadApprover);
        $request        = $this->approvalRequest;
        $getApprovalStepsWithActions = ($this->getApprovalStepsWithActions(steps: $steps, actions: $requestSteps, request: $request));
        if (isset($requestSteps)) {
            foreach ($steps as $step) {
                // Find the first step that has not been completed
                if ($requestSteps->where('action_type', ApprovalStatusEnum::REJECTED)->count()) {
                    return [
                        'step'    => null,
                        'request' => $request,
                    ];
                } elseif (!$requestSteps->contains('approval_flow_step_id', $step->id)) {
                    return [
                        'step'          => $step,
                        'request'       => $request,
                        'countSteps'    => $getApprovalStepsWithActions['count'],
                    ];
                }
            }
        }

        return null; // No steps left to approve
    }

    /**
     * Retrieve the last approval step and action, along with the associated approval request.
     * Returns an array containing the last step, the last action, and the approval request.
     * If there are no approval steps, returns an empty array.
     *
     * @return array{
     *     step: ?ApprovalFlowStep,
     *     action: ?ApprovalRequestAction,
     *     request: ApprovalRequest
     * }|[]
     */
    public function lastRequestAction(): array
    {
        return [
            'step'      => $this->getApprovalRequestSteps()?->last()?->approvalFlowStep,
            'action'    => $this->getApprovalRequestSteps()?->last(),
            'request'   => $this->approvalRequest,
        ];
    }

    /**
     * Retrieve the last step in the approval flow, along with the associated approval request.
     * Returns an array containing the last step and the approval request.
     * If there are no approval steps, returns an empty array.
     *
     * @return array{
     *     step: ?ApprovalFlowStep,
     *     request: ApprovalRequest
     * }|[]
     */
    public function lastStep(): array
    {
        return [
            'step'      => $this->getApprovalFlowSteps()?->last(),
            'request'   => $this->approvalRequest,
        ];
    }

    /**
     * Get the next step in the approval process.
     * Returns the first step that has not been completed, or the last step if all are completed.
     *
     * @return ApprovalFlowStep
     */
    public function nextStep()
    {
        $steps          = $this->getApprovalFlowSteps();
        $requestSteps   = $this->getApprovalRequestSteps();
        foreach ($steps as $step) {
            // Find the first step that has not been completed
            if (!$requestSteps->contains('approval_flow_step_id', $step->id)) {
                return $step;
            }
        }

        return $steps->last(); // Return the last step if all are completed
    }

    /**
     * Determine if the current user can approve this request.
     *
     * @return bool
     */
    public function canApprove(): bool
    {
        $nextStep = $this->nextStepWithRequest();
        if (!isset($nextStep['step'])) {
            return false; // No steps left to approve
        }

        $user = auth()->user();
        return $this->isUserAuthorized(
            $user,
            $this->getEmployee($nextStep['request']?->approvable?->employee_id),
            step: $nextStep['step'] ?? null,
            request: $nextStep['request'] ?? null
        );
    }

    /**
     * Retrieves the next approval step if the current user is authorized to approve it.
     * Retrieves the next approval step and checks if the current user is authorized
     * to approve it based on their role and the associated request.
     * If the user is authorized, returns an array containing the next step, the approval request,
     * and step counts. Otherwise, returns null.
     *
     * @param bool $loadApprover Whether to load the approver for the next step.
     * @return array{
     *     step: ?ApprovalFlowStep,
     *     request: ApprovalRequest,
     *     countSteps?: array{
     *         completed: int,
     *         uncompleted: int,
     *         current: int
     *     }
     * }|null
     */
    public function allowApproval($loadApprover = false)
    {
        $nextStep = $this->nextStepWithRequest($loadApprover);
        if (!isset($nextStep) || !isset($nextStep['step'])) {
            return null; // No steps left to approve
        }


        $employeeRequest = $this->getEmployee($nextStep['request']->approvable->employee_id);
        $currentUser = auth()->user();

        if ($this->isUserAuthorized(
            $currentUser,
            $employeeRequest,
            step: $nextStep['step'] ?? null,
            request: $nextStep['request'] ?? null
        )) {
            return $nextStep;
        }

        return null; // employee is not authorized
    }

    /**
     * Retrieves the next approval request if the current user is authorized to approve it.
     *
     * Retrieves the next approval step and checks if the current user is authorized
     * to approve it based on their role and the associated request.
     *
     * @return ApprovalRequest|null The next approval request if authorized, otherwise null.
     */
    public function getApprovalRequest(): ?ApprovalRequest
    {
        $nextStep = $this->nextStepWithRequest();
        if (!isset($nextStep) && !isset($nextStep['step'])) {
            return null; // No steps left to approve
        }

        $employeeRequest = $this->getEmployee($nextStep['request']->approvable->employee_id);
        $currentUser = auth()->user();
        if ($this->isUserAuthorized(
            $currentUser,
            $employeeRequest,
            step: $nextStep['step'] ?? null,
            request: $nextStep['request'] ?? null
        )) {
            return $nextStep['request']; // employee is authorized to approve this step
        }

        return null; // employee is not authorized
    }

    /**
     * Determines if the current user is authorized to cancel the last approval request.
     *
     * Determines if the current user is authorized to cancel the last approval request
     * based on their role and the associated request.
     *
     * @return bool True if authorized, otherwise false.
     */
    public function allowCancelApproval(): bool
    {
        $lastAction = $this->lastRequestAction();

        if (!$lastAction || !isset($lastAction['step']) || is_null($lastAction['step']) || !isset($lastAction['action'])  || !isset($lastAction['request']) || is_null($lastAction['request'])) {
            return false; // No steps left to cancel
        }

        if ($this->isUserAuthorized(
            auth()->user(),
            $this->getEmployee($lastAction['request']?->approvable?->employee_id),
            step: $lastAction['step'] ?? null,
            request: $lastAction['request'] ?? null
        )) {
            return true; // User is authorized to cancel this step
        }
        return false;
    }

    /**
     * Determine if the current user is authorized to approve the given step.
     *
     * Based on the type of the step, this method will check if the current user
     * is authorized to approve the given step. The types and their associated
     * logic are as follows:
     *
     * - User: Check if the current user is the same as the user specified in the step.
     * - Role: Check if the current user has any of the roles specified in the step.
     * - Permissions: Check if the current user has any of the permissions specified in the step.
     * - Sector: Check if the current user is the sector manager of the employee associated with the step.
     * - Division: Check if the current user is the division manager of the employee associated with the step.
     * - Department: Check if the current user is the department manager of the employee associated with the step.
     * - Section: Check if the current user is the section manager of the employee associated with the step.
     * - Department Request: Check if the current user is the manager of the department associated with the given request.
     * - Department Approval: Check if the current user is the manager of the department associated with the given request.
     *
     * @param User $currentUser The current user.
     * @param Employee|null $employee The employee associated with the step.
     * @param ApprovalFlowStep|null $step The approval flow step.
     * @param ApprovalRequest|null $request The approval request associated with the step.
     * @return bool True if authorized, otherwise false.
     */
    private function isUserAuthorized(User $currentUser, ?Employee $employee, ApprovalFlowStep|null $step, ?ApprovalRequest $request = null): bool
    {
        if ($currentUser->hasAnyRole('super-admin', 'general-manager', 'hr-manager')) {
            return true; // Super admin can approve any step
        }
        // return match ($step?->approver_type) {
        //     ApprovalTypeEnum::USER                   => $this->isHeThisUser($currentUser, $step?->user_id),
        //     ApprovalTypeEnum::ROLE                   => $this->isHasRoles($currentUser, $step?->roles),
        //     ApprovalTypeEnum::PERMISSIONS            => $this->isHasPermissions($currentUser, $step?->permissions),
        //     ApprovalTypeEnum::SECTOR                 => $this->isHeSectorManager($currentUser, $employee, $step?->manager_column),
        //     ApprovalTypeEnum::DIVISION               => $this->isHeDivisionManager($currentUser, $employee, $step?->manager_column),
        //     ApprovalTypeEnum::DEPARTMENT             => $this->isHeDepartmentManager($currentUser, $employee, $step?->manager_column),
        //     ApprovalTypeEnum::SECTION                => $this->isHeSectionManager($currentUser, $employee, $step?->manager_column),
        //     ApprovalTypeEnum::DEPARTMENT_REQUEST     => $this->isHeManagerDepartmentRequest($currentUser, $request->approvable, $step?->manager_column, $step?->approver_column),
        //     ApprovalTypeEnum::DEPARTMENT_APPROVAL    => $this->isHeManagerDepartmentApproval($currentUser, $request->approvable, $step?->manager_column, $step?->approver_column),
        //     default       => false,
        // };

        $item = $request?->approvable;

        return match ($step?->approver_type) {
            ApprovalTypeEnum::USER                   => $this->isHeThisUser($currentUser, $step?->user_id),
            ApprovalTypeEnum::ROLE                   => $this->isHasRoles($currentUser, $step?->roles),
            ApprovalTypeEnum::PERMISSIONS            => $this->isHasPermissions($currentUser, $step?->permissions),
            ApprovalTypeEnum::SECTOR                 => $this->isHeManagerFromLevelAndAbove($item, $currentUser, $employee, $step?->manager_column, 'sector'),
            ApprovalTypeEnum::DIVISION               => $this->isHeManagerFromLevelAndAbove($item, $currentUser, $employee, $step?->manager_column, 'division'),
            ApprovalTypeEnum::DEPARTMENT             => $this->isHeManagerFromLevelAndAbove($item, $currentUser, $employee, $step?->manager_column, 'department'),
            ApprovalTypeEnum::SECTION                => $this->isHeManagerFromLevelAndAbove($item, $currentUser, $employee, $step?->manager_column, 'section'),
            ApprovalTypeEnum::DEPARTMENT_REQUEST     => $this->isHeManagerDepartmentRequest($currentUser, $request->approvable, $step?->manager_column, $step?->approver_column),
            ApprovalTypeEnum::DEPARTMENT_APPROVAL    => $this->isHeManagerDepartmentApproval($currentUser, $request->approvable, $step?->manager_column, $step?->approver_column),
            default       => false,
        };
    }

    /**
     * Determines if the current user is a manager from a specified level and above.
     *
     * This method checks if the specified user is a manager at or above a given level
     * in the organizational hierarchy. The check is performed using the manager column
     * and the minimum level specified. The hierarchy is traversed starting from the given
     * minimum level, and the check is made at each level up to the company level.
     *
     * @param Object $item The item to check.
     * @param User $currentUser The current user.
     * @param Employee $employee The employee whose hierarchy is being checked.
     * @param string $managerColumn The column name representing the manager ID.
     * @param string $minLevel The minimum level in the hierarchy to start checking from.
     * @return bool True if the user is a manager from the specified level and above, otherwise false.
     */
    private function isHeManagerFromLevelAndAbove(Object $item, User $currentUser, ?Employee $employee, string $managerColumn, string $minLevel): bool
    {
        if (!$currentUser  || !$managerColumn || !$minLevel) {
            return false;
        }

        $position = $employee?->current_company_position ?? null;
        if (!$position && is_null($item)) return false;

        // get hierarchy
        $hierarchy = [
            'section'    => $position?->section ?? $item->section ?? null,
            'department' => $position?->department ?? $item->department ?? null,
            'division'   => $position?->division ?? $item->division ?? null,
            'sector'     => $position?->sector ?? $item->sector ?? null,
            // 'company'    => $position?->company ?? null,
        ];

        $startChecking = false;
        foreach ($hierarchy as $level => $model) {
            if ($level === $minLevel) {
                $startChecking = true;
            }

            // Check if the current user is the manager of the current level
            if ($startChecking && $model && isset($model->{$managerColumn}) && $model->{$managerColumn} === $currentUser->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the current user is the same as the given user ID.
     *
     * Given a current user and a user ID, this method checks if the current user
     * is the same as the user with the given ID. If the user ID is null, the method
     * returns false.
     * @param User $currentUser The current user.
     * @param int|null $user_id The user ID to check against.
     * @return bool True if the current user is the same as the given user ID, otherwise false.
     */
    private function isHeThisUser(User $currentUser, int|null $user_id): bool
    {
        if (is_null($user_id)) {
            return false;
        }
        return $currentUser->id === $user_id;
    }

    /**
     * Checks if the current user has any of the given roles.
     *
     * Given a current user and an array of role IDs, this method checks if the
     * current user has any of the given roles. If the current user has the role
     * of super-admin, the method returns true. Otherwise, the method checks if
     * the current user has any of the roles specified in the array using the
     * hasRole method of the User model.
     * @param User $currentUser The current user.
     * @param array $roles The array of role IDs to check against.
     * @return bool True if the current user has any of the given roles, otherwise false.
     */
    private function isHasRoles(User $currentUser, array $roles): bool
    {
        return $currentUser?->roles?->whereIn('id', $roles)?->count() > 0;
    }

    /**
     * Checks if the current user has any of the given permissions.
     *
     * Given a current user and an array of permission IDs, this method checks if the
     * current user has any of the given permissions. If the current user has the role
     * of super-admin, the method returns true. Otherwise, the method checks if
     * the current user has any of the permissions specified in the array using the
     * permissions relationship of the User model.
     *
     * @param User $currentUser The current user.
     * @param array $permissions The array of permission IDs to check against.
     * @return bool True if the current user has any of the given permissions, otherwise false.
     */
    private function isHasPermissions(User $currentUser, array $permissions): bool
    {
        return $currentUser?->permissions()?->whereIn('id', $permissions)?->exists();
    }

    /**
     * Checks if the current user is a sector manager for the given employee.
     *
     * This method determines whether the specified user is the manager of the sector
     * associated with the employee's current company position. The check is performed
     * based on the manager column provided.
     *
     * @param User $currentUser The current user.
     * @param Employee $employee The employee whose sector is being checked.
     * @param string|null $manager_column The column name representing the manager ID.
     * @return bool True if the user is the sector manager, otherwise false.
     */
    private function isHeSectorManager(User $currentUser, Employee $employee, string|null $manager_column): bool
    {
        if (!$currentUser || !$employee || is_null($manager_column)) {
            return false;
        }
        $sector = $employee?->current_company_position?->sector;
        return $sector && $sector->{$manager_column} === $currentUser->id;
    }

    /**
     * Checks if the current user is a division manager for the given employee.
     *
     * This method determines whether the specified user is the manager of the division
     * associated with the employee's current company position. The check is performed
     * based on the manager column provided.
     *
     * @param User $currentUser The current user.
     * @param Employee $employee The employee whose division is being checked.
     * @param string|null $manager_column The column name representing the manager ID.
     * @return bool True if the user is the division manager, otherwise false.
     */
    private function isHeDivisionManager(User $currentUser, Employee $employee, string|null $manager_column): bool
    {
        if (!$currentUser || !$employee || is_null($manager_column)) {
            return false;
        }
        $division = $employee?->current_company_position?->division;
        return $division && $division->{$manager_column} === $currentUser->id;
    }

    /**
     * Checks if the current user is a department manager for the given employee.
     *
     * This method determines whether the specified user is the manager of the department
     * associated with the employee's current company position. The check is performed
     * based on the manager column provided.
     *
     * @param User $currentUser The current user.
     * @param Employee $employee The employee whose department is being checked.
     * @param string|null $manager_column The column name representing the manager ID.
     * @return bool True if the user is the department manager, otherwise false.
     */
    private function isHeDepartmentManager(User $currentUser, Employee $employee, string|null $manager_column): bool
    {
        $department = $employee?->current_company_position?->department;
        return $department && $department->{$manager_column} === $currentUser->id;
    }

    /**
     * Checks if the current user is a section manager for the given employee.
     *
     * This method determines whether the specified user is the manager of the section
     * associated with the employee's current company position. The check is performed
     * based on the manager column provided.
     *
     * @param User $currentUser The current user.
     * @param Employee $employee The employee whose section is being checked.
     * @param string|null $manager_column The column name representing the manager ID.
     * @return bool True if the user is the section manager, otherwise false.
     */
    private function isHeSectionManager(User $currentUser, Employee $employee, string|null $manager_column): bool
    {
        if (!$currentUser || !$employee || is_null($manager_column)) {
            return false;
        }
        $section = $employee?->current_company_position?->section;
        return $section && $section->{$manager_column} === $currentUser->id;
    }

    /**
     * Checks if the current user is the manager of the department that is being requested to approve.
     *
     * This method determines whether the specified user is the manager of the department
     * associated with the item's approver ID. The check is performed
     * based on the manager column and the approver column provided.
     *
     * @param User $currentUser The current user.
     * @param mixed $item The item whose department is being checked.
     * @param string|null $manager_column The column name representing the manager ID.
     * @param string|null $approver_column The column name representing the approver ID.
     * @return bool True if the user is the manager of the department, otherwise false.
     */
    private function isHeManagerDepartmentRequest(User $currentUser, $item, string|null $manager_column, string|null $approver_column): bool
    {
        if (!$currentUser || is_null($manager_column) || is_null($approver_column)) {
            return false;
        }
        $department = Department::where('id', $item->{$approver_column})->first();
        if (!$department) {
            return false;
        }
        if ($item->{$approver_column} == $item->{$this->lastStep()['step']->approver_column} && $department->{$manager_column} === $currentUser->id) {
            return true;
        }
        return $department->{$manager_column} === $currentUser->id;
    }

    /**
     * Checks if the current user is the manager of the department that is being requested to approve.
     *
     * This method determines whether the specified user is the manager of the department
     * associated with the item's approver ID. The check is performed
     * based on the manager column and the approver column provided.
     *
     * @param User $currentUser The current user.
     * @param mixed $item The item whose department is being checked.
     * @param string|null $manager_column The column name representing the manager ID.
     * @param string|null $approver_column The column name representing the approver ID.
     * @return bool True if the user is the manager of the department, otherwise false.
     */
    private function isHeManagerDepartmentApproval(User $currentUser, $item, string|null $manager_column, string|null $approver_column): bool
    {
        if (!$currentUser || is_null($manager_column) || is_null($approver_column)) {
            return false;
        }
        $department = Department::where('id', $item->{$approver_column})->first();
        if (!$department) {
            return false;
        }
        if ($item->{$approver_column} == $item->{$this->lastStep()['step']->approver_column} && $department->{$manager_column} === $currentUser->id) {
            return true;
        }
        return $department->{$manager_column} === $currentUser->id;
    }

    /**
     * Answers the approval request with the given status and optional comment.
     * If the status is REJECTED, fires a RejectApproval event.
     * If the status is APPROVED and the request is not paused, completes the request.
     * If the status is APPROVED and the request is paused, fires a PauseApproval event.
     * If the status is any other value, returns false.
     * @param ApprovalStatusEnum $status The status to answer the request with.
     * @param ?string $comment An optional comment to add to the request action.
     * @param ?User $user The user performing the action. Defaults to the current user.
     * @return bool Whether the request was successfully answered.
     */
    public function answerRequest(ApprovalStatusEnum $status, ?string $comment = null, ?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        $nextStep = $this->nextStepWithRequest();

        $request_action =     $this->approvalRequestSteps()->create([
            'approval_flow_id'      => $nextStep['step']->approval_flow_id,
            'approval_flow_step_id' => $nextStep['step']->id,
            'action_type'           => $status->value,
            'approver_name'         => $user->name,
            'approver_id'           => $user->id,
            'comment'               => $comment,
        ]);

        if ($status->value == ApprovalStatusEnum::REJECTED->value) {
            event(new RejectApproval($this, $this->approvalRequest, $user));
            return true;
        }

        if ($request_action) {
            $this->completeRequest($nextStep, $status);
            return true;
        }
        return false;
    }

    /**
     * Complete the approval request if the given status is the last step.
     * If the request is completed, fires an ApprovalCompleted event.
     * If the request is paused, fires a PauseApproval event.
     * If the request is not completed, returns false.
     * @param array|null $nextStep The next step in the request.
     * @param ApprovalStatusEnum $status The status to complete the request with.
     * @return bool Whether the request was successfully completed.
     */
    private function completeRequest($nextStep, ApprovalStatusEnum $status): bool
    {
        if (
            isset($nextStep['countSteps']['uncompleted']) &&
            $nextStep['countSteps']['uncompleted'] == 1
        ) {
            $approval_request = $this->approvalRequest()->update([
                'status' => $status,
            ]);

            event(new ApprovalCompleted($this, $this->approvalRequest, auth()->user()));

            return $approval_request;
        } elseif (
            isset($nextStep['countSteps']['completed']) &&
            $nextStep['countSteps']['completed'] == 0
        ) {
            event(new FirstApprovalCompleted($this, $this->approvalRequest, auth()->user()));
        }

        return false;
    }

    /**
     * Cancels the completion of the approval request.
     *
     * Cancels the completion of the approval request by setting the status of the
     * request to PENDING and firing a CancelApprovalCompleted event.
     *
     * @return bool True if the approval request was cancelled, otherwise false.
     */
    public function cancalCompleteRequest(): bool
    {
        $nextStep = $this->nextStepWithRequest();
        if (
            isset($nextStep['countSteps']['uncompleted']) &&
            $nextStep['countSteps']['uncompleted'] == 1
        ) {
            $request_action = $this->approvalRequest()?->update([
                'status' => ApprovalStatusEnum::PENDING->value,
            ]);

            event(new CancelApprovalCompleted($this, $this->approvalRequest, auth()->user()));

            return $request_action;
        }
        return false;
    }

    /**
     * Cancels the approval request.
     *
     * Cancels the approval request by deleting the last approval step and
     * cancelling the complete request.
     *
     * @return bool True if the approval request was cancelled, otherwise false.
     */
    public function cancalApproval(): bool
    {
        $request_action = $this->approvalRequestSteps()?->orderBy('id', 'desc')->first();
        if ($request_action) {
            $request_action?->delete();

            $this->cancalCompleteRequest();

            event(new CancelApproval($this, $this->approvalRequest, auth()->user()));

            return true;
        }

        return false;
    }

    /**
     * Determines if the approval request is completed.
     *
     * Checks if the approval request status is APPROVED.
     *
     * @return bool True if the approval request is completed, otherwise false.
     */
    public function isApproved(): bool
    {
        return $this->approvalRequest?->status == ApprovalStatusEnum::APPROVED;
    }
}
