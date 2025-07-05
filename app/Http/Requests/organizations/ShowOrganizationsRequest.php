<?php

declare(strict_types=1);

namespace App\Http\Requests\Organizations;

use App\Http\Requests\Traits\WithFieldValidation;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Organizations;

class ShowOrganizationsRequest extends FormRequest
{
    use WithFieldValidation;

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
            'columns' => $this->getColumnsRule(),
        ];
    }

    protected function model(): Organizations
    {
        return new Organizations();
    }
}
