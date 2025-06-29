<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $userId],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'employee_id' => ['sometimes', 'string', 'max:50', 'unique:users,employee_id,' . $userId],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => __('validation.user.name.max'),
            'email.email' => __('validation.user.email.email'),
            'email.unique' => __('validation.user.email.unique'),
            'password.confirmed' => __('validation.user.password.confirmed'),
            'phone.max' => __('validation.user.phone.max'),
            'employee_id.max' => __('validation.user.employee_id.max'),
            'employee_id.unique' => __('validation.user.employee_id.unique'),
            'is_active.boolean' => __('validation.user.is_active.boolean'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('attributes.user.name'),
            'email' => __('attributes.user.email'),
            'password' => __('attributes.user.password'),
            'phone' => __('attributes.user.phone'),
            'employee_id' => __('attributes.user.employee_id'),
            'is_active' => __('attributes.user.is_active'),
        ];
    }
}
