<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShoppingListItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'nullable|numeric|min:0',
            'unit_code' => 'nullable|exists:units,code',
            'is_checked' => 'nullable|boolean',
        ];
    }
}
