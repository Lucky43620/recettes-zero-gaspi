<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function getQuery(): string
    {
        return $this->input('q', '');
    }

    public function getPage(): int
    {
        return (int) $this->input('page', 1);
    }
}
