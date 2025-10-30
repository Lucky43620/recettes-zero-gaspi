<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealPlanRecipe extends Model
{
    protected $fillable = [
        'meal_plan_id',
        'recipe_id',
        'day_of_week',
        'meal_type',
        'servings',
        'notes',
    ];

    protected $casts = [
        'servings' => 'integer',
    ];

    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
