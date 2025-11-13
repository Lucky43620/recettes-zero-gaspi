<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipe_id' => 'required|exists:recipes,id',
        ];
    }
}
