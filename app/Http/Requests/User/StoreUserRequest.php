<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Traits\WithDynamicFieldValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    use WithDynamicFieldValidation;

    /**
     * 確定用戶是否有權限進行此請求
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 取得驗證規則
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users,employee_id'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * 取得驗證錯誤訊息
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.user.name.required'),
            'name.max' => __('validation.user.name.max'),
            'email.required' => __('validation.user.email.required'),
            'email.email' => __('validation.user.email.email'),
            'email.unique' => __('validation.user.email.unique'),
            'password.required' => __('validation.user.password.required'),
            'password.confirmed' => __('validation.user.password.confirmed'),
            'phone.max' => __('validation.user.phone.max'),
            'employee_id.max' => __('validation.user.employee_id.max'),
            'employee_id.unique' => __('validation.user.employee_id.unique'),
            'is_active.boolean' => __('validation.user.is_active.boolean'),
        ];
    }

    /**
     * 取得欄位名稱
     */
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
