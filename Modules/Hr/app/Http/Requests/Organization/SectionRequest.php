<?php

namespace Modules\Hr\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
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
            'department_id' => ['nullable', 'exists:departments,id'],
            'division_id'   => ['nullable', 'exists:divisions,id'],
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
            'department_id' => __('hr.department'),
            'division_id'   => __('hr.division'),
            'description'   => __('hr.description'),
        ];
    }
}
