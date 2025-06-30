<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'min:60'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => __('validation.auth.token.required'),
            'token.min' => __('validation.auth.token.min'),
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

    /**
     * 自定義驗證規則
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 驗證 token 是否有效
            $status = Password::tokenExists($this->email, $this->token);
            if (!$status) {
                $validator->errors()->add('token', '密碼重設連結無效或已過期');
            }
        });
    }
}
