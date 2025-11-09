<?php

namespace Modules\Hr\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class PublicHolidayRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'start_date'            => ['required', 'date'],
            'end_date'              => ['required', 'date', 'after_or_equal:start_date'],
            'actual_start_date'     => ['required', 'date'],
            'actual_end_date'       => ['required', 'date', 'after_or_equal:actual_start_date'],
            'days'                  => ['required', 'integer'],
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
            'start_date'            => __('hr.start_date'),
            'end_date'              => __('hr.end_date'),
            'actual_start_date'     => __('hr.actual_start_date'),
            'actual_end_date'       => __('hr.actual_end_date'),
            'days'                  => __('hr.days'),
        ];
    }
}
