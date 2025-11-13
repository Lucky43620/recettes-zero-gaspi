<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCooksnapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => 'nullable|string|max:1000',
            'photos' => 'required|array|min:1|max:5',
            'photos.*' => 'required|image|max:10240',
        ];
    }
}
