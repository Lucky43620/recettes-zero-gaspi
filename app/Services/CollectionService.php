<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;

class CollectionService
{
    public function createCollection(User $user, array $data): Collection
    {
        return $user->collections()->create($data);
    }

    public function updateCollection(Collection $collection, array $data): Collection
    {
        $collection->update($data);
        return $collection->fresh();
    }

    public function addRecipe(Collection $collection, Recipe $recipe): void
    {
        if ($this->recipeExists($collection, $recipe)) {
            throw new \Exception('Cette recette est déjà dans la collection');
        }

        $maxPosition = $collection->recipes()->max('collection_recipe.position') ?? -1;

        $collection->recipes()->attach($recipe->id, [
            'position' => $maxPosition + 1,
            'added_at' => now(),
        ]);
    }

    public function removeRecipe(Collection $collection, Recipe $recipe): void
    {
        $collection->recipes()->detach($recipe->id);
    }

    public function recipeExists(Collection $collection, Recipe $recipe): bool
    {
        return $collection->recipes()->where('recipe_id', $recipe->id)->exists();
    }

    public function reorderRecipes(Collection $collection, array $recipeIds): void
    {
        \DB::transaction(function () use ($collection, $recipeIds) {
            foreach ($recipeIds as $position => $recipeId) {
                $collection->recipes()
                    ->updateExistingPivot($recipeId, ['position' => $position + 1]);
            }
        });
    }

    public function getUserCollections(User $user, int $perPage = 20)
    {
        return $user->collections()
            ->withCount('recipes')
            ->latest()
            ->paginate($perPage);
    }

    public function getCollectionWithRecipes(Collection $collection)
    {
        return $collection->load([
            'user',
            'recipes' => function ($query) {
                $query->with(['author', 'media'])
                    ->orderBy('collection_recipe.position');
            }
        ]);
    }

    public function deleteCollection(Collection $collection): void
    {
        $collection->recipes()->detach();
        $collection->delete();
    }
}
