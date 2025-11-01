<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePantryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_code' => 'required|exists:units,code',
            'expiration_date' => 'nullable|date|after_or_equal:today',
            'storage_location' => 'nullable|string|max:255',
            'opened' => 'boolean',
        ];
    }
}
