<?php

namespace App\Services;

use App\Models\MealPlan;
use App\Models\User;
use Carbon\Carbon;

class MealPlanService
{
    public function duplicateMealPlan(User $user, MealPlan $sourcePlan, string $newWeekStartDate): MealPlan
    {
        $weekStart = Carbon::parse($newWeekStartDate)->startOfWeek();

        $existingPlan = $user->mealPlans()
            ->where('week_start_date', $weekStart)
            ->first();

        if ($existingPlan) {
            $existingPlan->mealPlanRecipes()->delete();
            $targetPlan = $existingPlan;
        } else {
            $targetPlan = $user->mealPlans()->create([
                'week_start_date' => $weekStart,
            ]);
        }

        foreach ($sourcePlan->mealPlanRecipes as $mealPlanRecipe) {
            $targetPlan->mealPlanRecipes()->create([
                'recipe_id' => $mealPlanRecipe->recipe_id,
                'planned_date' => $mealPlanRecipe->planned_date,
                'meal_type' => $mealPlanRecipe->meal_type,
                'servings' => $mealPlanRecipe->servings,
                'notes' => $mealPlanRecipe->notes,
            ]);
        }

        return $targetPlan->load('mealPlanRecipes.recipe');
    }
}
