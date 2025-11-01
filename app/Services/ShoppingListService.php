<?php

namespace App\Services;

use App\Models\MealPlan;
use App\Models\ShoppingList;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ShoppingListService
{
    public function generateFromMealPlan(MealPlan $mealPlan, int $userId): ShoppingList
    {
        $mealPlan->load('mealPlanRecipes.recipe.ingredients.unit');

        $ingredients = $this->aggregateIngredients($mealPlan);

        $weekStart = Carbon::parse($mealPlan->week_start)->format('d/m/Y');

        $shoppingList = ShoppingList::create([
            'user_id' => $userId,
            'name' => "Liste - Semaine du {$weekStart}",
            'meal_plan_id' => $mealPlan->id,
        ]);

        foreach ($ingredients as $ingredient) {
            $shoppingList->items()->create($ingredient);
        }

        return $shoppingList;
    }

    private function aggregateIngredients(MealPlan $mealPlan): Collection
    {
        $ingredients = collect();

        foreach ($mealPlan->mealPlanRecipes as $mealPlanRecipe) {
            foreach ($mealPlanRecipe->recipe->ingredients as $ingredient) {
                $key = $ingredient->id . '_' . ($ingredient->pivot->unit_code ?? 'none');

                $servingMultiplier = $mealPlanRecipe->servings / $mealPlanRecipe->recipe->servings;
                $adjustedQuantity = $ingredient->pivot->quantity * $servingMultiplier;

                if ($ingredients->has($key)) {
                    $existing = $ingredients->get($key);
                    $existing['quantity'] += $adjustedQuantity;
                    $ingredients->put($key, $existing);
                } else {
                    $ingredients->put($key, [
                        'name' => $ingredient->name,
                        'quantity' => $adjustedQuantity,
                        'unit_code' => $ingredient->pivot->unit_code,
                    ]);
                }
            }
        }

        return $ingredients;
    }

    public function optimizeQuantities(Collection $ingredients): Collection
    {
        return $ingredients->map(function ($ingredient) {
            $quantity = $ingredient['quantity'];

            if ($quantity < 0.01) {
                $ingredient['quantity'] = round($quantity, 3);
            } elseif ($quantity < 1) {
                $ingredient['quantity'] = round($quantity, 2);
            } else {
                $ingredient['quantity'] = round($quantity, 1);
            }

            return $ingredient;
        });
    }
}
