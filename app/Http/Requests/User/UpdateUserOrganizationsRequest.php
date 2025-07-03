<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserOrganizationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_ids' => ['required', 'array'],
            'organization_ids.*' => ['string', 'exists:organizations,id'],
        ];
    }
}
