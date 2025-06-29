<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Traits\WithFieldValidation;
use Illuminate\Foundation\Http\FormRequest;

class ShowUserRequest extends FormRequest
{
    use WithFieldValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'columns' => $this->getColumnsRule(),
            'columns.*' => $this->getColumnsEachRule(),
            'with' => ['array'],
            'with.*' => ['string', 'in:profile,posts'],
        ];
    }

    public function messages(): array
    {
        return [
            'columns.array' => __('validation.selection.columns.array'),
            'columns.*.in' => __('validation.selection.columns_each.in'),
            'with.array' => __('validation.selection.with.array'),
            'with.*.in' => __('validation.selection.with_each.in'),
        ];
    }

    public function attributes(): array
    {
        return [
            'columns' => __('attributes.selection.columns'),
            'with' => __('attributes.selection.with'),
        ];
    }
}
