<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PantryRecipeSearchResource extends JsonResource
{
    private array $pantryIngredientIds;

    public function __construct($resource, array $pantryIngredientIds = [])
    {
        parent::__construct($resource);
        $this->pantryIngredientIds = $pantryIngredientIds;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $recipeIngredientIds = $this->ingredients->pluck('id')->toArray();
        $matchingIds = array_intersect($recipeIngredientIds, $this->pantryIngredientIds);
        $missingIds = array_diff($recipeIngredientIds, $this->pantryIngredientIds);

        $matchingIngredients = $this->ingredients->whereIn('id', $matchingIds)->values();
        $missingIngredients = $this->ingredients->whereIn('id', $missingIds)->values();

        $matchPercentage = count($recipeIngredientIds) > 0
            ? round((count($matchingIds) / count($recipeIngredientIds)) * 100)
            : 0;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'summary' => $this->summary,
            'servings' => $this->servings,
            'prep_minutes' => $this->prep_minutes,
            'cook_minutes' => $this->cook_minutes,
            'difficulty' => $this->difficulty,
            'average_rating' => $this->rating_avg,
            'image' => $this->media->first()?->getUrl() ?? null,
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ],
            'matching_ingredients_count' => count($matchingIds),
            'total_ingredients_count' => count($recipeIngredientIds),
            'missing_ingredients_count' => count($missingIds),
            'match_percentage' => $matchPercentage,
            'matching_ingredients' => $matchingIngredients->map(fn($i) => [
                'id' => $i->id,
                'name' => $i->name,
            ]),
            'missing_ingredients' => $missingIngredients->map(fn($i) => [
                'id' => $i->id,
                'name' => $i->name,
            ]),
            'can_make_with_pantry' => count($missingIds) === 0,
        ];
    }
}
