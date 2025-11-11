<?php

namespace Modules\Hr\Http\Requests\MasterData;

use Modules\Hr\Enums\LeaveTypeUnitEnum;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Hr\Enums\LeaveTypeDurationEnum;

class LeaveTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'                              => ['required', 'string', 'max:255'],
            'unit'                              => ['required', 'in:' . implode(',', array_map(fn($case) => $case->value, LeaveTypeUnitEnum::cases()))],
            'type_duration'                     => ['required', 'in:' . implode(',', array_map(fn($case) => $case->value, LeaveTypeDurationEnum::cases()))],
            'company_amount'                    => ['required', 'numeric', 'min:0', 'max:100'],
            'duration_deducted_percentage'      => ['required', 'numeric', 'min:0', 'max:100'],
            'salary_deducted_percentage'        => ['required', 'numeric', 'min:0', 'max:100'],
            'for_employee'                      => ['required', 'boolean'],
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
            'name'                              => __('hr.name'),
            'unit'                              => __('hr.unit'),
            'type_duration'                     => __('hr.type_duration'),
            'company_amount'                    => __('hr.company_amount'),
            'duration_deducted_percentage'      => __('hr.duration_deducted_percentage'),
            'salary_deducted_percentage'        => __('hr.salary_deducted_percentage'),
            'for_employee'                      => __('hr.for_employee'),
        ];
    }
}
