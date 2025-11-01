<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlan extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'week_start_date',
    ];

    protected $casts = [
        'week_start_date' => 'date:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mealPlanRecipes(): HasMany
    {
        return $this->hasMany(MealPlanRecipe::class);
    }

    public function shoppingLists(): HasMany
    {
        return $this->hasMany(ShoppingList::class);
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'meal_plan_recipes')
            ->withPivot(['planned_date', 'meal_type', 'servings', 'notes'])
            ->withTimestamps();
    }

    public function scopeForWeek($query, $weekStart)
    {
        return $query->where('week_start_date', $weekStart);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithRecipes($query)
    {
        return $query->with(['mealPlanRecipes.recipe.media', 'mealPlanRecipes.recipe.author']);
    }
}
