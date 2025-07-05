<?php

declare(strict_types=1);

namespace App\Http\Requests\Organizations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\OrganizationStatus;

class UpdateOrganizationsRequest extends FormRequest
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
            'parent_id' => ['nullable', 'ulid', 'exists:organizations,id'],
            'manager_user_id' => ['nullable', 'ulid', 'exists:users,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0'],
            'approval_settings' => ['nullable', 'json'],
            'settings' => ['nullable', 'json'],
            'cost_center_code' => ['nullable', 'string', 'max:255'],
            'status' => [Rule::in(OrganizationStatus::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => __('validation.organizations.name.string'),
            'name.max' => __('validation.organizations.name.max'),
            'type.string' => __('validation.organizations.type.string'),
            'type.max' => __('validation.organizations.type.max'),
            'parent_id.exists' => __('validation.organizations.parent_id.exists'),
            'manager_user_id.exists' => __('validation.organizations.manager_user_id.exists'),
            'address.string' => __('validation.organizations.address.string'),
            'address.max' => __('validation.organizations.address.max'),
            'phone.string' => __('validation.organizations.phone.string'),
            'phone.max' => __('validation.organizations.phone.max'),
            'email.email' => __('validation.organizations.email.email'),
            'email.max' => __('validation.organizations.email.max'),
            'monthly_budget.numeric' => __('validation.organizations.monthly_budget.numeric'),
            'monthly_budget.min' => __('validation.organizations.monthly_budget.min'),
            'approval_settings.json' => __('validation.organizations.approval_settings.json'),
            'settings.json' => __('validation.organizations.settings.json'),
            'cost_center_code.string' => __('validation.organizations.cost_center_code.string'),
            'cost_center_code.max' => __('validation.organizations.cost_center_code.max'),
            'status.in' => __('validation.organizations.status.in'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('attributes.organizations.name'),
            'type' => __('attributes.organizations.type'),
            'parent_id' => __('attributes.organizations.parent_id'),
            'manager_user_id' => __('attributes.organizations.manager_user_id'),
            'address' => __('attributes.organizations.address'),
            'phone' => __('attributes.organizations.phone'),
            'email' => __('attributes.organizations.email'),
            'monthly_budget' => __('attributes.organizations.monthly_budget'),
            'approval_settings' => __('attributes.organizations.approval_settings'),
            'settings' => __('attributes.organizations.settings'),
            'cost_center_code' => __('attributes.organizations.cost_center_code'),
            'status' => __('attributes.organizations.status'),
        ];
    }
}
