<?php

namespace Modules\usermanagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalFlowRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            "name"                                  => ['required', 'string', 'max:255', 'min:2'],
            'redirect_route'                        => ['required', 'string', 'max:255', 'min:2'],
            "approvable_type"                       => ['required', 'string', 'max:255', 'min:2'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            "name"                                  => __('usermanagement.name'),
            'redirect_route'                        => __('usermanagement.redirect_route'),
            "approvable_type"                       => __('usermanagement.approvable_type'),
        ];
    }
}
