<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealPlanRecipe extends Model
{
    protected $fillable = [
        'meal_plan_id',
        'recipe_id',
        'planned_date',
        'meal_type',
        'servings',
        'notes',
    ];

    protected $casts = [
        'planned_date' => 'date:Y-m-d',
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
