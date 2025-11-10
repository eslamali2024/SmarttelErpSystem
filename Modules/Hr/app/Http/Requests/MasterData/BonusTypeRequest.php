<?php

namespace Modules\Hr\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class BonusTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
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
            'name'                  => __('hr.name'),
        ];
    }
}
