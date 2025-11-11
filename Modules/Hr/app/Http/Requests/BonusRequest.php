<?php

namespace Modules\Hr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BonusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'employee_id'          => ['required', 'exists:employees,id'],
            'bonus_type_id'        => ['required', 'exists:bonus_types,id'],
            'amount'               => ['required', 'numeric'],
            'date'                 => ['required', 'date'],
            'notes'                => ['nullable', 'string', 'max:2000'],
            'reason'               => ['nullable', 'string', 'max:255'],
            'redirect_url'         => ['nullable', 'url'],
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
            'employee_id'          => __('hr.employee'),
            'bonus_type_id'        => __('hr.bonus_type'),
            'amount'               => __('hr.amount'),
            'date'                 => __('hr.date'),
            'notes'                => __('hr.notes'),
            'reason'               => __('hr.reason'),
        ];
    }
}
