<?php

namespace Modules\Hr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
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
}
