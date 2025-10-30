<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeStep extends Model
{
    protected $fillable = [
        'recipe_id',
        'position',
        'text',
        'timer_minutes',
    ];

    protected $casts = [
        'position' => 'integer',
        'timer_minutes' => 'integer',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
