<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected $appends = ['day_of_week'];

    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    protected function dayOfWeek(): Attribute
    {
        return Attribute::make(
            get: function () {
                $daysMap = [
                    0 => 'monday',
                    1 => 'tuesday',
                    2 => 'wednesday',
                    3 => 'thursday',
                    4 => 'friday',
                    5 => 'saturday',
                    6 => 'sunday',
                ];

                $weekStart = Carbon::parse($this->mealPlan->week_start_date);
                $plannedDate = Carbon::parse($this->planned_date);
                $dayIndex = $weekStart->diffInDays($plannedDate);

                return $daysMap[$dayIndex] ?? 'monday';
            }
        );
    }
}
