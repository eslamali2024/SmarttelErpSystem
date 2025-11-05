<?php

namespace Modules\Hr\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class InsuranceCompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['nullable', 'string', 'email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:255'],
            'website'       => ['nullable', 'string', 'max:255', 'url'],
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
            'name'          => __('hr.name'),
            'email'         => __('hr.email'),
            'phone'         => __('hr.phone'),
            'address'       => __('hr.address'),
            'website'       => __('hr.website'),
        ];
    }
}
