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
            'step_1'            => ['required', 'array'],
            'step_1.code'                   => ['required', 'string', 'max:255', 'unique:employees,code,' . $this->employee?->id],
            'step_1.name'                   => ['required', 'string', 'max:255'],
            'step_1.name_ar'                => ['required', 'string', 'max:255'],
            'step_1.email'                  => ['required', 'string', 'email', 'max:255', 'unique:employees,email,' . $this->employee?->id],
            'step_1.gender'                 => ['required', 'in:' . implode(',', GenderEnum::values())],
            'step_1.marital_status'         => ['required', 'in:' . implode(',', MaritalStatusEnum::values())],
            'step_1.phone'                  => ['required', 'string', 'max:255'],
            'step_1.joining_date'           => ['required', 'date'],
            'step_1.national_id'            => ['required', 'string', 'size:14'],
            'step_1.dob'                    => ['required', 'date'],
            'step_1.address'                => ['required', 'string', 'max:2000'],
            'step_1.notes'                  => ['nullable', 'string', 'max:2000'],

            'step_2'            => ['required', 'array'],
            'step_2.division_id'            => ['required', 'exists:divisions,id'],
            'step_2.department_id'          => ['required', 'exists:departments,id'],
            'step_2.section_id'             => ['required', 'exists:sections,id'],
            'step_2.position_id'            => ['required', 'exists:positions,id'],
            'step_2.start_date'             => ['required', 'date'],
            'step_2.end_date'               => ['required', 'date'],
            'step_2.notes'                  => ['nullable', 'max:2000'],

            'step_3'            => ['required', 'array'],
            'step_3.basic_salary'               => ['required', 'numeric', 'min:0'],
            'step_3.net_salary'                 => ['required', 'numeric', 'min:0'],
            'step_3.gross_salary'               => ['required', 'numeric', 'min:0'],
            'step_3.insurance_wage'             => ['required', 'numeric', 'min:0'],
            'step_3.insurance_wage_rounded'     => ['required', 'numeric', 'min:0'],
            'step_3.tax_monthly'                => ['required', 'numeric', 'min:0'],
            'step_3.social_insurance'           => ['required', 'numeric', 'min:0'],
            'step_3.martyrs_families_fund'      => ['required', 'numeric', 'min:0'],
            'step_3.company_insurance'          => ['required', 'numeric', 'min:0'],
            'step_3.salary_per_year'            => ['required', 'numeric', 'min:0'],
            'step_3.salary_per_day'             => ['required', 'numeric', 'min:0'],
            'step_3.salary_per_hour'            => ['required', 'numeric', 'min:0'],
            'step_3.total_deductions'           => ['required', 'numeric', 'min:0'],
            'step_3.recurring_allowances'       => ['nullable', 'array'],
            'step_3.off_cycle_allowances'       => ['nullable', 'array'],
            'step_3.insurance_number'           => ['nullable', 'string', 'max:255'],
            'step_3.insurance_company_id'       => ['nullable', 'exists:insurance_companies,id'],
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
            'step_1.name'                   => __('hr.name'),
            'step_1.name_ar'                => __('hr.name_ar'),
            'step_1.email'                  => __('hr.email'),
            'step_1.gender'                 => __('hr.gender'),
            'step_1.marital_status'         => __('hr.marital_status'),
            'step_1.phone'                  => __('hr.phone'),
            'step_1.joining_date'           => __('hr.joining_date'),
            'step_1.national_id'            => __('hr.national_id'),
            'step_1.dob'                    => __('hr.dob'),
            'step_1.address'                => __('hr.address'),
            'step_1.notes'                  => __('hr.notes'),

            'step_2.division_id'            => __('hr.division'),
            'step_2.department_id'          => __('hr.department'),
            'step_2.section_id'             => __('hr.section'),
            'step_2.position_id'            => __('hr.position'),
            'step_2.start_date'             => __('hr.start_date'),
            'step_2.end_date'               => __('hr.end_date'),
            'step_2.notes'                  => __('hr.notes'),

            'step_3.net_salary'                 => __('hr.net_salary'),
            'step_3.gross_salary'              => __('hr.gross_salary'),
            'step_3.insurance_wage'             => __('hr.insurance_wage'),
            'step_3.insurance_wage_rounded'     => __('hr.insurance_wage_rounded'),
            'step_3.tax_monthly'                => __('hr.tax_monthly'),
            'step_3.social_insurance'           => __('hr.social_insurance'),
            'step_3.martyrs_families_fund'      => __('hr.martyrs_families_fund'),
            'step_3.company_insurance'          => __('hr.company_insurance'),
            'step_3.salary_per_year'            => __('hr.salary_per_year'),
            'step_3.salary_per_day'             => __('hr.salary_per_day'),
            'step_3.salary_per_hour'            => __('hr.salary_per_hour'),
            'step_3.total_deductions'         => __('hr.total_deductions'),


        ];
    }
}
