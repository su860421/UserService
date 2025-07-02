<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.auth.name.required'),
            'name.max' => __('validation.auth.name.max'),
            'email.required' => __('validation.auth.email.required'),
            'email.email' => __('validation.auth.email.email'),
            'email.unique' => __('validation.auth.email.unique'),
            'password.required' => __('validation.auth.password.required'),
            'password.min' => __('validation.auth.password.min'),
            'password.confirmed' => __('validation.auth.password.confirmed'),
            'phone.max' => __('validation.auth.phone.max'),
            'employee_id.unique' => __('validation.auth.employee_id.unique'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('attributes.auth.name'),
            'email' => __('attributes.auth.email'),
            'password' => __('attributes.auth.password'),
            'phone' => __('attributes.auth.phone'),
            'employee_id' => __('attributes.auth.employee_id'),
        ];
    }
}
