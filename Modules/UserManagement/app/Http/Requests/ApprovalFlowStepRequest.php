<?php

namespace Modules\UserManagement\Http\Requests;

use App\Enums\Approval\ApprovalTypeEnum as ApprovalApprovalTypeEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ApprovalFlowStepRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'                 => ['required', 'string'],
            "roles"                => [Rule::requiredIf($this->approver_type == ApprovalApprovalTypeEnum::ROLE->value), 'array'],
            'permissions'          => [Rule::requiredIf($this->approver_type == ApprovalApprovalTypeEnum::PERMISSIONS->value), 'array'],
            'manager_column'       => [Rule::requiredIf(!in_array($this->approver_type, [ApprovalApprovalTypeEnum::ROLE->value, ApprovalApprovalTypeEnum::USER->value, ApprovalApprovalTypeEnum::PERMISSIONS->value]))],
            'approver_column'      => [Rule::requiredIf(in_array($this->approver_type, [ApprovalApprovalTypeEnum::DEPARTMENT_REQUEST->value, ApprovalApprovalTypeEnum::DEPARTMENT_APPROVAL->value]))],
            "user_id"              => [Rule::requiredIf($this->approver_type == ApprovalApprovalTypeEnum::USER->value)],
            "approver_type"        => ['required', 'integer', Rule::in(ApprovalApprovalTypeEnum::cases())],
            "order"                => ['required', 'integer'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name'                 => __('usermanagement.name'),
            "roles"                => __('usermanagement.roles'),
            'permissions'          => __('usermanagement.permissions'),
            'column_name'          => __('usermanagement.column_name'),
            "user_id"              => __('usermanagement.user'),
            "approver_type"        => __('usermanagement.approver_type'),
            "order"                => __('usermanagement.order'),
        ];
    }
}
