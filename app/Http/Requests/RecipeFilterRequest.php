<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'sort' => 'nullable|in:latest,rating,duration,popular',
            'visibility' => 'nullable|in:public,private',
        ];
    }

    public function filters(): array
    {
        return [
            'search' => $this->input('search', ''),
            'difficulty' => $this->input('difficulty', ''),
            'sort' => $this->input('sort', 'latest'),
            'visibility' => $this->input('visibility', ''),
        ];
    }
}
