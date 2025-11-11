<?php

namespace Modules\UserManagement\Services;

use App\Models\User;
use App\Models\Approval\ApprovalFlow;
use Modules\UserManagement\Models\Role;
use App\Enums\Approval\ApprovalTypeEnum;
use Spatie\Permission\Models\Permission;
use App\Models\Approval\ApprovalFlowStep;

class ApprovalFlowStepService
{
    /**
     * Create a new step.
     *
     * @param array<string, mixed>  $data
     * @return ApprovalFlowStep
     * @return ApprovalFlowStep
     */
    public function store(ApprovalFlow $flow, array $data): ApprovalFlowStep
    {
        return $flow->approvalFlowSteps()->create($data);
    }

    /**
     * Update a step.
     *
     * @param ApprovalFlowStep $step
     * @param array<string, mixed>  $data
     * @return ApprovalFlowStep
     */
    public function update(ApprovalFlowStep $step, array $data): bool
    {
        return  $step->update([
            'name'               => $data['name'] ?? null,
            'roles'              => isset($data['approver_type']) && $data['approver_type'] == ApprovalTypeEnum::ROLE->value ? $data['roles'] : null,
            'permissions'        => isset($data['approver_type']) && $data['approver_type'] == ApprovalTypeEnum::PERMISSIONS->value ? $data['permissions'] : null,
            'approver_type'      => $data['approver_type'] ?? null,
            'approver_column'    => isset($data['approver_type']) &&  !in_array($data['approver_type'], [ApprovalTypeEnum::ROLE->value, ApprovalTypeEnum::USER->value, ApprovalTypeEnum::PERMISSIONS->value]) ? $data['approver_column'] : null,
            'order'              => $data['order'] ?? null,
            'user_id'            => isset($data['approver_type']) && $data['approver_type'] == ApprovalTypeEnum::USER->value ? $data['user_id'] : null
        ]);
    }

    /**
     * Delete a step.
     *
     * @param ApprovalFlowStep  $step
     * @return bool
     */
    public function destroy(ApprovalFlowStep $step): bool
    {
        return $step->delete();
    }

    /**
     * Retrieves all data required for the approval flow steps form.
     *
     * @return array{
     *     'approver_types' => array<string, string>,
     *     'roles' => array<string, int>,
     *     'permissions' => array<string, int>,
     *     'users' => array<string, int>
     * }
     */
    public function getAllData()
    {
        return [
            'approver_types' => ApprovalTypeEnum::items(),
            'roles'          => Role::pluck('name')->map(fn($name) => ['value' => $name, 'label' => $name]),
            'permissions'    => Permission::pluck('name')->map(fn($name) => ['value' => $name, 'label' => $name]),
            'users'          => User::pluck('name', 'id'),
        ];
    }
}
