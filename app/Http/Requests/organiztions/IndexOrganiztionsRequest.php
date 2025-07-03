<?php

declare(strict_types=1);

namespace App\Http\Requests\Organiztions;

use Illuminate\Foundation\Http\FormRequest;

class IndexOrganiztionsRequest extends FormRequest
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
            'per_page' => ['integer', 'min:1', 'max:100'],
            'order_by' => ['string'],
            'order_direction' => ['in:asc,desc'],
            'with' => ['array'],
            'columns' => ['array'],
            'filters' => ['array'],
        ];
    }
}
