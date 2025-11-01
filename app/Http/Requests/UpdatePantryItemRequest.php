<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePantryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|numeric|min:0.01',
            'unit_code' => 'required|exists:units,code',
            'expiration_date' => 'nullable|date',
            'storage_location' => 'nullable|string|max:255',
            'opened' => 'boolean',
        ];
    }
}
