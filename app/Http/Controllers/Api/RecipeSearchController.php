<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PantryRecipeSearchResource;
use App\Services\RecipeService;
use Illuminate\Http\Request;

class RecipeSearchController extends Controller
{
    public function __construct(
        private RecipeService $recipeService
    ) {}

    public function searchWithPantryIngredients(Request $request)
    {
        $user = $request->user();

        $pantryIngredientIds = $user->pantryItems()
            ->pluck('ingredient_id')
            ->unique()
            ->toArray();

        if (empty($pantryIngredientIds)) {
            return response()->json([
                'data' => [],
                'message' => 'Votre garde-manger est vide. Ajoutez des ingrédients pour trouver des recettes !',
                'pantry_ingredients_count' => 0,
            ]);
        }

        $recipes = $this->recipeService->searchWithPantryIngredients($pantryIngredientIds);

        $data = $recipes->map(function ($recipe) use ($pantryIngredientIds) {
            return (new PantryRecipeSearchResource($recipe, $pantryIngredientIds))->resolve();
        });

        return response()->json([
            'data' => $data,
            'pantry_ingredients_count' => count($pantryIngredientIds),
        ]);
    }
}
