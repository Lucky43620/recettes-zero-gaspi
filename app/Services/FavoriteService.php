<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\User;

class FavoriteService
{
    public function toggle(User $user, Recipe $recipe): bool
    {
        if ($this->isFavorited($user, $recipe)) {
            $this->removeFavorite($user, $recipe);
            return false;
        }

        $this->addFavorite($user, $recipe);
        return true;
    }

    public function addFavorite(User $user, Recipe $recipe): void
    {
        if (!$this->isFavorited($user, $recipe)) {
            $user->favorites()->attach($recipe->id);
        }
    }

    public function removeFavorite(User $user, Recipe $recipe): void
    {
        $user->favorites()->detach($recipe->id);
    }

    public function isFavorited(User $user, Recipe $recipe): bool
    {
        return $user->favorites()->where('recipe_id', $recipe->id)->exists();
    }

    public function getUserFavorites(User $user, int $perPage = 12)
    {
        return $user->favorites()
            ->with(['author', 'media'])
            ->where('is_public', true)
            ->latest('favorites.created_at')
            ->paginate($perPage);
    }

    public function getFavoriteIds(User $user): array
    {
        return $user->favorites()->pluck('recipe_id')->toArray();
    }
}
