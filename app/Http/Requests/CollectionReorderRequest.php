<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CollectionReorderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipe_ids' => 'required|array',
            'recipe_ids.*' => 'required|exists:recipes,id',
        ];
    }

    public function getRecipeIds(): array
    {
        return $this->input('recipe_ids', []);
    }
}
