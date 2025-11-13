<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeService
{
    public function createRecipe(array $validated): Recipe
    {
        $recipe = Auth::user()->recipes()->create($this->extractRecipeData($validated));
        return $this->syncRecipeRelations($recipe, $validated);
    }

    public function updateRecipe(Recipe $recipe, array $validated): Recipe
    {
        $recipe->update($this->extractRecipeData($validated));
        return $this->syncRecipeRelations($recipe, $validated);
    }

    private function extractRecipeData(array $validated): array
    {
        return [
            'title' => $validated['title'],
            'summary' => $validated['summary'] ?? null,
            'servings' => $validated['servings'],
            'prep_minutes' => $validated['prep_minutes'] ?? null,
            'cook_minutes' => $validated['cook_minutes'] ?? null,
            'difficulty' => $validated['difficulty'] ?? null,
            'cuisine' => $validated['cuisine'] ?? null,
            'is_public' => $validated['is_public'] ?? true,
            'calories' => $validated['calories'] ?? null,
            'nutrients' => $validated['nutrients'] ?? null,
        ];
    }

    private function syncRecipeRelations(Recipe $recipe, array $validated): Recipe
    {
        $this->syncSteps($recipe, $validated['steps']);

        if (!empty($validated['ingredients'])) {
            $this->syncIngredients($recipe, $validated['ingredients']);
        }

        if (!empty($validated['images'])) {
            $this->syncImages($recipe, $validated['images']);
        }

        return $recipe;
    }

    public function applyFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): \Illuminate\Database\Eloquent\Builder
    {
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('summary', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->has('sort')) {
            match ($request->sort) {
                'rating' => $query->orderBy('rating_avg', 'desc'),
                'duration' => $query->orderByRaw('COALESCE(prep_minutes, 0) + COALESCE(cook_minutes, 0) ASC'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        return $query;
    }

    private function syncSteps(Recipe $recipe, array $steps): void
    {
        $recipe->steps()->delete();

        foreach ($steps as $index => $step) {
            $recipe->steps()->create([
                'position' => $index + 1,
                'text' => $step['text'],
                'timer_minutes' => $step['timer_minutes'] ?? null,
            ]);
        }
    }

    private function syncIngredients(Recipe $recipe, array $ingredients): void
    {
        $recipe->ingredients()->detach();

        foreach ($ingredients as $index => $ingredientData) {
            // Handle both ingredient_id (from tests/API) and name (from form)
            if (!empty($ingredientData['ingredient_id'])) {
                $ingredient = Ingredient::find($ingredientData['ingredient_id']);
            } elseif (!empty($ingredientData['name'])) {
                $ingredient = Ingredient::firstOrCreate(
                    ['name' => $ingredientData['name']]
                );
            } else {
                continue;
            }

            if (!$ingredient) {
                continue;
            }

            $recipe->ingredients()->attach($ingredient->id, [
                'quantity' => $ingredientData['quantity'] ?? null,
                'unit_code' => $ingredientData['unit_code'] ?? null,
                'position' => $index,
            ]);
        }
    }

    private function syncImages(Recipe $recipe, array $images): void
    {
        $recipe->clearMediaCollection('images');

        foreach ($images as $image) {
            $recipe->addMedia($image)->toMediaCollection('images');
        }
    }

    public function searchWithPantryIngredients(array $pantryIngredientIds)
    {
        $placeholders = implode(',', array_fill(0, count($pantryIngredientIds), '?'));

        return Recipe::where('is_public', true)
            ->with(['author', 'media', 'ingredients'])
            ->select('recipes.*')
            ->selectRaw("
                (SELECT COUNT(DISTINCT ingredient_id)
                 FROM recipe_ingredients
                 WHERE recipe_ingredients.recipe_id = recipes.id
                 AND ingredient_id IN ({$placeholders})) as matching_ingredients_count
            ", $pantryIngredientIds)
            ->selectRaw('
                (SELECT COUNT(DISTINCT ingredient_id)
                 FROM recipe_ingredients
                 WHERE recipe_ingredients.recipe_id = recipes.id) as total_ingredients_count
            ')
            ->selectRaw("
                ((SELECT COUNT(DISTINCT ingredient_id)
                  FROM recipe_ingredients
                  WHERE recipe_ingredients.recipe_id = recipes.id
                  AND ingredient_id IN ({$placeholders})) * 100.0 /
                 NULLIF((SELECT COUNT(DISTINCT ingredient_id)
                        FROM recipe_ingredients
                        WHERE recipe_ingredients.recipe_id = recipes.id), 0)) as match_percentage
            ", $pantryIngredientIds)
            ->groupBy('recipes.id')
            ->havingRaw("
                (SELECT COUNT(DISTINCT ingredient_id)
                 FROM recipe_ingredients
                 WHERE recipe_ingredients.recipe_id = recipes.id
                 AND ingredient_id IN ({$placeholders})) > 0
            ", $pantryIngredientIds)
            ->orderByDesc('matching_ingredients_count')
            ->orderByDesc('match_percentage')
            ->limit(50)
            ->get();
    }
}
