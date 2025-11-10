<?php

namespace Modules\Hr\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class WorkScheduleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'                              => ['required', 'string', 'max:255'],
            'start_time'                        => ['required'],
            'end_time'                          => ['required'],
            'allow_late_sign_in'                => ['required'],
            'allow_early_sign_out'              => ['required'],
            'days'                              => ['required', 'array', 'min:1'],
            'rules'                             => ['sometimes', 'array'],
            'rules.*'                           => ['required', 'array'],
            'rules.*.deducation_after_time'     => ['required'],
            'rules.*.deducation_percentage'     => ['required'],
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
            'name'                              => __('hr.name'),
            'start_time'                        => __('hr.start_time'),
            'end_time'                          => __('hr.end_time'),
            'allow_late_sign_in'                => __('hr.allow_late_sign_in'),
            'allow_early_sign_out'              => __('hr.allow_early_sign_out'),
            'days.*.day'                        => __('hr.day'),
            'days.*.work'                       => __('hr.work'),
            'rules.*.deducation_after_time'     => __('hr.deducation_after_time'),
            'rules.*.deducation_percentage'     => __('hr.deducation_percentage'),
        ];
    }
}
