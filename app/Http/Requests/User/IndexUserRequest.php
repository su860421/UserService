<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Traits\WithFieldValidation;
use Illuminate\Foundation\Http\FormRequest;

class IndexUserRequest extends FormRequest
{
    use WithFieldValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->getIndexRules();
    }

    public function messages(): array
    {
        return $this->getIndexMessages();
    }

    public function attributes(): array
    {
        return $this->getIndexAttributes();
    }

    protected function getAvailableFields(): array
    {
        return $this->getModelFields(\App\Models\User::class);
    }

    protected function model(): \App\Models\User
    {
        return new \App\Models\User();
    }
}
