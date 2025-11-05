<?php

namespace Modules\Hr\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class DivisionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'code'          => ['nullable', 'string', 'max:255'],
            'name'          => ['required', 'string', 'max:255'],
            'manager_id'    => ['nullable', 'exists:users,id'],
            'description'   => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'code'          => __('hr.code'),
            'name'          => __('hr.name'),
            'manager_id'    => __('hr.manager'),
            'description'   => __('hr.description'),
        ];
    }
}
