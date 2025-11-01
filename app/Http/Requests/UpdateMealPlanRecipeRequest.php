<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMealPlanRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meal_type' => 'nullable|in:breakfast,lunch,dinner,snack',
            'planned_date' => 'nullable|date',
            'servings' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ];
    }
}
