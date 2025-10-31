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
            'steps' => 'required|array|min:1',
            'steps.*.text' => 'required|string',
            'steps.*.timer_minutes' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|max:10240',
        ];
    }
}
