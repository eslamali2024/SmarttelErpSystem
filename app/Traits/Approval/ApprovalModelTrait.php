<?php

namespace App\Traits\Approval;

use App\Models\User;
use Modules\Hr\Models\Section;
use Modules\Hr\Models\Division;
use Modules\Hr\Models\Department;
use App\Enums\Approval\ApprovalTypeEnum;
use App\Models\Approval\ApprovalRequest;
use App\Enums\Approval\ApprovalStatusEnum;
use App\Notifications\Approval\NewApprovalRequestNotification;
use App\Notifications\Approval\HappenRejectOnRequestNotification;
use App\Notifications\Approval\FinishedApprovalRequestNotification;

trait ApprovalModelTrait
{
    /**
     * Handles actions to be performed when an approval request is created.
     *
     * Sends a new approval request notification to the approver.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User|null $user The user who created the approval request.
     */
    public function onCreateRequestApproval(ApprovalRequest $request, ?User $user = null): void
    {
        $firstStep = $request->getFirstStep();
        $users = $this->getUsersByStep($request, $firstStep);
        $item = $request->approvable()->first();
        if ($users && $item) {
            foreach ($users as $user) {
                if (isset($item)) {
                    $user->notify(new NewApprovalRequestNotification($item, $request, $user));

                    // broadcast(new SendNotificationEvent([
                    //     'title' => 'New Approval Request',
                    //     'message' => 'You have a new approval request',
                    // ], $user->id))->toOthers();
                }
            }
        }
    }

    /**
     * Handles actions to be performed when an approval is first started.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who started the approval.
     */
    public function onCompletedFirstApprove(ApprovalRequest $request, User $user): void
    {
        $request->approvable()->update([
            'status' => ApprovalStatusEnum::IN_PROGRESS
        ]);

        $item = $request->approvable()->first();
        if (isset($item)) {
            $firstStep =  $item->nextStepWithRequest()['step'] ?? null;
            $users = $this->getUsersByStep($request, $firstStep);
            if ($users) {
                foreach ($users as $user) {
                    $user->notify(new NewApprovalRequestNotification($item, $request, $user));

                    // broadcast(new SendNotificationEvent([
                    //     'title' => 'New Approval Request',
                    //     'message' => 'You have a new approval request',
                    // ], $user->id))->toOthers();
                }
            }
        }
    }

    /**
     * Handles actions to be performed when an approval is completed.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who completed the approval.
     */
    public function onCompletedApprove(ApprovalRequest $request, User $user): void
    {
        $request->approvable()->update([
            'status' => ApprovalStatusEnum::APPROVED
        ]);

        $item = $request->approvable()->first();
        if (isset($item)) {
            if ($user) {
                $user = $item->employee?->user;
                $user?->notify(new FinishedApprovalRequestNotification($item, $request, $user));

                // broadcast(new SendNotificationEvent([
                //     'title' => 'Finished Approval Request',
                //     'message' => 'Your approval request has been finished',
                // ], $user?->id))->toOthers();
            }
        }
    }

    /**
     * Handles actions to be performed when an approval is canceled.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who canceled the approval.
     */
    public function onCancelCompletedApprove(ApprovalRequest $request, User $user): void
    {
        $request->approvable()->update([
            'status' => ApprovalStatusEnum::IN_PROGRESS
        ]);
    }

    /**
     * Handles actions to be performed when an approval is canceled.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who canceled the approval.
     *
     */
    public function onCancelApprove(ApprovalRequest $request, User $user): void
    {
        $request->approvable()->update([
            'status' => ApprovalStatusEnum::IN_PROGRESS
        ]);
    }

    /**
     * Handles actions to be performed when an approval is rejected.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who rejected the approval.
     */
    public function onHappenReject(ApprovalRequest $request, User $user): void
    {
        $request->approvable()->update([
            'status' => ApprovalStatusEnum::REJECTED
        ]);
        $item = $request->approvable()->first();
        if (isset($item)) {
            $user = $request->approvable()->first()?->employee?->user;
            if ($user) {
                $user?->notify(new HappenRejectOnRequestNotification($item, $request, $user));

                // broadcast(new SendNotificationEvent([
                //     'title' => 'Rejected Approval Request',
                //     'message' => 'Your approval request has been rejected',
                // ], $user?->id))->toOthers();
            }
        }
    }

    /**
     * Retrieve the users associated with the given approval step.
     *
     * Based on the type of the step, this method will retrieve the users
     * associated with the step. The types and their associated logic are as follows:
     *
     * - Role: Retrieves the users with the given role(s).
     * - User: Retrieves the user with the given id.
     * - Department: Retrieves the user who is the manager of the department
     *   associated with the given employee.
     * - Section: Retrieves the user who is the manager of the section associated
     *   with the given employee.
     * - Sector: Retrieves the user who is the manager of the sector associated
     *   with the given employee.
     * - Division: Retrieves the user who is the manager of the division associated
     *   with the given employee.
     * - Department Request: Retrieves the user who is the manager of the department
     *   associated with the given employee, and the department is the same as the
     *   one specified in the step.
     * - Department Approval: Retrieves the user who is the manager of the department
     *   associated with the given employee, and the department is the same as the
     *   one specified in the step.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param ApprovalFlowStep $step The approval flow step instance.
     * @return Collection<int, User> The users associated with the step.
     */
    private function getUsersByStep(ApprovalRequest $request, $step)
    {
        $employeePosition = $request?->approvable()?->first()?->employee?->current_company_position;

        return match ($step?->approver_type) {
            ApprovalTypeEnum::ROLE          => User::whereHas('roles', fn($query) => $query->whereIn('id', $step->roles))->get(),
            ApprovalTypeEnum::PERMISSIONS   => User::whereHas('roles', fn($query) => $query->whereIn('id', $step->permissions))->get(),
            ApprovalTypeEnum::USER          => User::where('id', $step->approver->id)->get(),
            ApprovalTypeEnum::DEPARTMENT    => User::where(
                'id',
                Department::where('id', $employeePosition?->department_id)->first()?->{$step->manager_column}
            )->get(),
            ApprovalTypeEnum::SECTION    => User::where(
                'id',
                Section::where('id', $employeePosition?->section_id)->first()?->{$step->manager_column}
            )->get(),
            ApprovalTypeEnum::DIVISION    => User::where(
                'id',
                Division::where('id', $employeePosition?->division_id)->first()?->{$step->manager_column}
            )->get(),
            ApprovalTypeEnum::DEPARTMENT_REQUEST    => User::where(
                'id',
                Department::where('id', $step->{$step->approver_column})->first()?->{$step->manager_column}
            )->get(),
            ApprovalTypeEnum::DEPARTMENT_APPROVAL    => User::where(
                'id',
                Department::where('id', $step->{$step->approver_column})->first()?->{$step->manager_column}
            )->get(),
            default => []
        };
    }

    /*
     * Retrieves the approval status for the given model.
     *
     * An array containing the following:
     * - approval: A boolean indicating if the current user is authorized to approve the given model.
     * - cancel_approval: A boolean indicating if the current user is authorized to cancel the approval of the given model.
     * - last_request_action_approval: An array containing the last approval step and action, along with the associated approval request.
     * - approval_steps: An array containing the approval flow steps for the given model, along with the associated approval request actions, and user counts.
     *
     * @return array
     */
    public function getApproval(): array
    {
        return [
            'approval'                      => $this->allowApproval(),
            'cancel_approval'               => $this->allowCancelApproval(),
            'approval_steps'                => $this->getApprovalStepsWithActions(loadApprover: true),
            'last_request_action_approval'  => $this->lastRequestAction(),
        ];
    }
}
