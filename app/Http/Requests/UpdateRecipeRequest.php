<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('recipe'));
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'servings' => 'required|integer|min:1',
            'prep_minutes' => 'nullable|integer|min:0',
            'cook_minutes' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'cuisine' => 'nullable|string|max:100',
            'is_public' => 'boolean',
            'calories' => 'nullable|integer|min:0',
            'nutrients' => 'nullable|array',
            'steps' => 'nullable|array',
            'steps.*.text' => 'required|string',
            'steps.*.timer_minutes' => 'nullable|numeric|min:0',
            'ingredients' => 'nullable|array',
            'ingredients.*.ingredient_id' => 'nullable|exists:ingredients,id',
            'ingredients.*.name' => 'nullable|string|max:255',
            'ingredients.*.quantity' => 'nullable|numeric|min:0',
            'ingredients.*.unit_code' => 'nullable|string|exists:units,code',
            'images' => 'nullable|array',
            'images.*' => 'image|max:10240',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('ingredients')) {
                foreach ($this->input('ingredients', []) as $index => $ingredient) {
                    if (empty($ingredient['ingredient_id']) && empty($ingredient['name'])) {
                        $validator->errors()->add(
                            "ingredients.{$index}",
                            'Each ingredient must have either an ingredient_id or a name.'
                        );
                    }
                }
            }
        });
    }
}
