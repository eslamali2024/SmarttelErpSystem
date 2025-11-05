<?php

namespace Modules\Hr\Http\Requests\MasterData;

use Modules\Hr\Enums\AllowancesTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Hr\Enums\AllowancesTaxableEnum;

class AllowanceTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'type'          => ['required', 'in:' . implode(',', AllowancesTypeEnum::values())],
            'taxable'       => ['required', 'in:' . implode(',', AllowancesTaxableEnum::values())],
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
            'type'          => __('hr.type'),
            'taxable'       => __('hr.taxable'),
        ];
    }
}
