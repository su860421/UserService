<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Traits\WithFieldValidation;
use Illuminate\Foundation\Http\FormRequest;

class ShowUserRequest extends FormRequest
{
    use WithFieldValidation;

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
            'columns' => $this->getColumnsRule(),
            'columns.*' => $this->getColumnsEachRule(),
            'with' => ['array'],
            'with.*' => ['string', 'in:profile,posts'],
        ];
    }

    /**
     * 取得驗證錯誤訊息
     */
    public function messages(): array
    {
        return [
            'columns.array' => __('validation.selection.columns.array'),
            'columns.*.in' => __('validation.selection.columns_each.in'),
            'with.array' => __('validation.selection.with.array'),
            'with.*.in' => __('validation.selection.with_each.in'),
        ];
    }

    /**
     * 取得欄位名稱
     */
    public function attributes(): array
    {
        return [
            'columns' => __('attributes.selection.columns'),
            'with' => __('attributes.selection.with'),
        ];
    }
}
