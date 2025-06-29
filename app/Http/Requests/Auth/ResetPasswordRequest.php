<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => __('validation.auth.token.required'),
            'email.required' => __('validation.auth.email.required'),
            'email.email' => __('validation.auth.email.email'),
            'password.required' => __('validation.auth.password.required'),
            'password.min' => __('validation.auth.password.min'),
            'password.confirmed' => __('validation.auth.password.confirmed'),
        ];
    }

    public function attributes(): array
    {
        return [
            'token' => __('attributes.auth.token'),
            'email' => __('attributes.auth.email'),
            'password' => __('attributes.auth.password'),
        ];
    }
}
