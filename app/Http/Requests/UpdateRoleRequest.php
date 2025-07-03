<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('id');
        return [
            'name' => ['required', 'string', 'unique:roles,name,' . $roleId],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.role.name.required'),
            'name.unique' => __('validation.role.name.unique'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('attributes.role.name'),
            'description' => __('attributes.role.description'),
        ];
    }
}
