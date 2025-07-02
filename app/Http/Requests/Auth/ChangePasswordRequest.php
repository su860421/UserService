<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => __('validation.auth.current_password.required'),
            'password.required' => __('validation.auth.password.required'),
            'password.min' => __('validation.auth.password.min'),
            'password.confirmed' => __('validation.auth.password.confirmed'),
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => __('attributes.auth.current_password'),
            'password' => __('attributes.auth.password'),
        ];
    }
}
