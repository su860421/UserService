<?php

declare(strict_types=1);

namespace App\Http\Requests\Organiztions;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganiztionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'ulid', 'exists:organiztions,id'],
            'manager_user_id' => ['nullable', 'ulid', 'exists:users,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0'],
            'approval_settings' => ['nullable', 'json'],
            'settings' => ['nullable', 'json'],
            'cost_center_code' => ['nullable', 'string', 'max:255'],
            'status' => ['in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => __('validation.organiztions.name.string'),
            'name.max' => __('validation.organiztions.name.max'),
            'type.string' => __('validation.organiztions.type.string'),
            'type.max' => __('validation.organiztions.type.max'),
            'parent_id.exists' => __('validation.organiztions.parent_id.exists'),
            'manager_user_id.exists' => __('validation.organiztions.manager_user_id.exists'),
            'address.string' => __('validation.organiztions.address.string'),
            'address.max' => __('validation.organiztions.address.max'),
            'phone.string' => __('validation.organiztions.phone.string'),
            'phone.max' => __('validation.organiztions.phone.max'),
            'email.email' => __('validation.organiztions.email.email'),
            'email.max' => __('validation.organiztions.email.max'),
            'monthly_budget.numeric' => __('validation.organiztions.monthly_budget.numeric'),
            'monthly_budget.min' => __('validation.organiztions.monthly_budget.min'),
            'approval_settings.json' => __('validation.organiztions.approval_settings.json'),
            'settings.json' => __('validation.organiztions.settings.json'),
            'cost_center_code.string' => __('validation.organiztions.cost_center_code.string'),
            'cost_center_code.max' => __('validation.organiztions.cost_center_code.max'),
            'status.in' => __('validation.organiztions.status.in'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('attributes.organiztions.name'),
            'type' => __('attributes.organiztions.type'),
            'parent_id' => __('attributes.organiztions.parent_id'),
            'manager_user_id' => __('attributes.organiztions.manager_user_id'),
            'address' => __('attributes.organiztions.address'),
            'phone' => __('attributes.organiztions.phone'),
            'email' => __('attributes.organiztions.email'),
            'monthly_budget' => __('attributes.organiztions.monthly_budget'),
            'approval_settings' => __('attributes.organiztions.approval_settings'),
            'settings' => __('attributes.organiztions.settings'),
            'cost_center_code' => __('attributes.organiztions.cost_center_code'),
            'status' => __('attributes.organiztions.status'),
        ];
    }
}
