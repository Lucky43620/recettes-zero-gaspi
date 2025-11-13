<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
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
            'reportable_type' => 'required|string|in:App\Models\Recipe,App\Models\Comment,App\Models\Cooksnap',
            'reportable_id' => 'required|integer',
            'reason' => 'required|string|in:spam,inappropriate,offensive,misleading,copyright,other',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
