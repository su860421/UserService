<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignRolesToUserRequest extends FormRequest
{
    public function authorize()
    {
        // 可依需求調整權限驗證
        return true;
    }

    public function rules()
    {
        return [
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ];
    }

    public function messages()
    {
        return [
            'roles.required' => __('validation.user.roles.required'),
            'roles.*.exists' => __('validation.user.roles.exists'),
        ];
    }
}
