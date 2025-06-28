<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\WithFieldValidation;
use App\Models\User;

class IndexUserRequest extends FormRequest
{
    use WithFieldValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function model()
    {
        return new User();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge($this->getIndexRules(), [
            'with.*' => ['string', 'in:profile,posts'],
        ]);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return $this->getIndexMessages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return $this->getIndexAttributes();
    }

    /**
     * Get available fields
     */
    protected function getAvailableFields(): array
    {
        return $this->getModelFields(User::class);
    }
}
