<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMealPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipe_id' => 'required|exists:recipes,id',
            'planned_date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'servings' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ];
    }
}
