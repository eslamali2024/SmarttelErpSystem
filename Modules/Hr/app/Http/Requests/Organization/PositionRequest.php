<?php

namespace Modules\Hr\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'code'          => ['nullable', 'string', 'max:255'],
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:2000'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'division_id'   => ['nullable', 'exists:divisions,id'],
            'section_id'    => ['nullable', 'exists:sections,id'],
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
            'description'   => __('hr.description'),
            'department_id' => __('hr.department'),
            'division_id'   => __('hr.division'),
            'section_id'    => __('hr.section'),
        ];
    }
}
