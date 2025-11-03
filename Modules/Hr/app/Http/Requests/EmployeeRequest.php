<?php

namespace Modules\Hr\Http\Requests;

use Modules\Hr\Enums\GenderEnum;
use Modules\Hr\Enums\MaritalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'step_1' => ['required', 'array'],
            'step_1.employee_code' => ['required', 'string', 'max:255'],
            'step_1.name'          => ['required', 'string', 'max:255'],
            'step_1.name_ar'       => ['required', 'string', 'max:255'],
            'step_1.email'         => ['required', 'string', 'email', 'max:255'],
            'step_1.gender'        => ['required', 'in:' . implode(',', GenderEnum::items())],
            'step_1.marital_status' => ['required', 'in:' . implode(',', MaritalStatusEnum::items())],
            'step_1.phone'         => ['required', 'string', 'max:255'],
            'step_1.joining_date'  => ['required', 'date'],
            'step_1.national_id'   => ['required', 'string', 'min:14', 'max:14'],
            'step_1.dob'           => ['required', 'date'],
            'step_1.address'       => ['required', 'string', 'max:2000'],
            'step_1.notes'         => ['nullable', 'string', 'max:2000'],

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
            'step_1.name'          => __('hr.name'),
            'step_1.name_ar'       => __('hr.name_ar'),
            'step_1.email'         => __('hr.email'),
            'step_1.gender'        => __('hr.gender'),
            'step_1.marital_status' => __('hr.marital_status'),
            'step_1.phone'         => __('hr.phone'),
            'step_1.joining_date'  => __('hr.joining_date'),
            'step_1.national_id'   => __('hr.national_id'),
            'step_1.dob'           => __('hr.dob'),
            'step_1.address'       => __('hr.address'),
            'step_1.notes'         => __('hr.notes'),
        ];
    }
}
