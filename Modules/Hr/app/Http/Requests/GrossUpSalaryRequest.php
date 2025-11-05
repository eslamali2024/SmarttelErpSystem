<?php

namespace Modules\Hr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrossUpSalaryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'net_salary' => ['required', 'numeric', 'min:0'],
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
            'net_salary' => __('hr.net_salary'),
        ];
    }
}
