<?php

namespace App\Http\Requests\Approval;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     *  @return bool
     */
    public function authorize(): bool
    {
        return  true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     *  @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */    public function rules(): array
    {
        $rules = [
            'comment'                   => ['nullable', 'string', 'max:2550'],
            'redirect_url'              => ['nullable', 'url'],
        ];

        return $rules;
    }

    /**
     * Get the error attributes for the defined validation rules.
     *
     *  @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status'                    => __('dashboard.status'),
            'comment'                   => __('dashboard.comment'),
        ];
    }
}
