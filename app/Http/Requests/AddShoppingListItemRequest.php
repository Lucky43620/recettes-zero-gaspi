<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddShoppingListItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ingredient_id' => 'nullable|exists:ingredients,id',
            'name' => 'nullable|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'unit_code' => 'nullable|exists:units,code',
        ];
    }
}
