<?php

namespace Modules\Hr\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class TimeManagementRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'payroll'           => ['required', 'boolean'],
            'fingerprint_in'    => ['required', 'boolean'],
            'fingerprint_out'   => ['required', 'boolean'],
            'factors'           => ['required', 'boolean'],
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
            'name'              => __('hr.name'),
            'payroll'           => __('hr.payroll'),
            'fingerprint_in'    => __('hr.fingerprint_in'),
            'fingerprint_out'   => __('hr.fingerprint_out'),
            'factors'           => __('hr.factors'),
        ];
    }
}
