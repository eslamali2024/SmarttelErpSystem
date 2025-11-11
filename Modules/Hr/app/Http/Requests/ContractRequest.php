<?php

namespace Modules\Hr\Http\Requests;

use Modules\Hr\Enums\GenderEnum;
use Modules\Hr\Enums\MaritalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'redirect_url'      => ['nullable', 'url'],
            'step_1'            => ['required', 'array'],
            'step_1.employee_id'            => ['required', 'exists:employees,id'],
            'step_1.division_id'            => ['required', 'exists:divisions,id'],
            'step_1.department_id'          => ['required', 'exists:departments,id'],
            'step_1.section_id'             => ['required', 'exists:sections,id'],
            'step_1.position_id'            => ['required', 'exists:positions,id'],
            'step_1.start_date'             => ['required', 'date'],
            'step_1.end_date'               => ['required', 'date'],
            'step_1.notes'                  => ['nullable', 'max:2000'],
            'step_1.time_management_id'     => ['required', 'exists:time_managements,id'],
            'step_1.work_schedule_id'       => ['required', 'exists:work_schedules,id'],

            'step_2'            => ['required', 'array'],
            'step_2.basic_salary'               => ['required', 'numeric', 'min:0'],
            'step_2.net_salary'                 => ['required', 'numeric', 'min:0'],
            'step_2.gross_salary'               => ['required', 'numeric', 'min:0'],
            'step_2.insurance_wage'             => ['required', 'numeric', 'min:0'],
            'step_2.insurance_wage_rounded'     => ['required', 'numeric', 'min:0'],
            'step_2.tax_monthly'                => ['required', 'numeric', 'min:0'],
            'step_2.social_insurance'           => ['required', 'numeric', 'min:0'],
            'step_2.martyrs_families_fund'      => ['required', 'numeric', 'min:0'],
            'step_2.company_insurance'          => ['required', 'numeric', 'min:0'],
            'step_2.salary_per_year'            => ['required', 'numeric', 'min:0'],
            'step_2.salary_per_day'             => ['required', 'numeric', 'min:0'],
            'step_2.salary_per_hour'            => ['required', 'numeric', 'min:0'],
            'step_2.total_deductions'           => ['required', 'numeric', 'min:0'],
            'step_2.recurring_allowances'       => ['nullable', 'array'],
            'step_2.off_cycle_allowances'       => ['nullable', 'array'],
            'step_2.insurance_number'           => ['nullable', 'string', 'max:255'],
            'step_2.insurance_company_id'       => ['nullable', 'exists:insurance_companies,id'],
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
            'step_1.employee_id'            => __('hr.employee'),
            'step_1.division_id'            => __('hr.division'),
            'step_1.department_id'          => __('hr.department'),
            'step_1.section_id'             => __('hr.section'),
            'step_1.position_id'            => __('hr.position'),
            'step_1.start_date'             => __('hr.start_date'),
            'step_1.end_date'               => __('hr.end_date'),
            'step_1.notes'                  => __('hr.notes'),
            'step_1.time_management_id'     => __('hr.time_management'),
            'step_1.work_schedule_id'       => __('hr.work_schedule'),

            'step_2.net_salary'                 => __('hr.net_salary'),
            'step_2.gross_salary'               => __('hr.gross_salary'),
            'step_2.insurance_wage'             => __('hr.insurance_wage'),
            'step_2.insurance_wage_rounded'     => __('hr.insurance_wage_rounded'),
            'step_2.tax_monthly'                => __('hr.tax_monthly'),
            'step_2.social_insurance'           => __('hr.social_insurance'),
            'step_2.martyrs_families_fund'      => __('hr.martyrs_families_fund'),
            'step_2.company_insurance'          => __('hr.company_insurance'),
            'step_2.salary_per_year'            => __('hr.salary_per_year'),
            'step_2.salary_per_day'             => __('hr.salary_per_day'),
            'step_2.salary_per_hour'            => __('hr.salary_per_hour'),
            'step_2.total_deductions'         => __('hr.total_deductions'),
        ];
    }
}
