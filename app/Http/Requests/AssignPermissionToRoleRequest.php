<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPermissionToRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.required' => __('validation.role.permissions.required'),
            'permissions.*.exists' => __('validation.role.permissions.exists'),
        ];
    }

    public function attributes(): array
    {
        return [
            'permissions' => __('attributes.role.permissions'),
        ];
    }
}
