<?php

namespace App\Services;

use App\Models\Rating;
use App\Models\Recipe;
use App\Models\User;

class RatingService
{
    public function addOrUpdateRating(User $user, Recipe $recipe, int $rating): void
    {
        Rating::updateOrCreate(
            [
                'user_id' => $user->id,
                'recipe_id' => $recipe->id,
            ],
            [
                'rating' => $rating,
            ]
        );

        $this->updateRecipeRatingStats($recipe);
    }

    public function removeRating(User $user, Recipe $recipe): void
    {
        Rating::where('user_id', $user->id)
            ->where('recipe_id', $recipe->id)
            ->delete();

        $this->updateRecipeRatingStats($recipe);
    }

    private function updateRecipeRatingStats(Recipe $recipe): void
    {
        $stats = $recipe->ratings()
            ->selectRaw('AVG(rating) as avg, COUNT(*) as count')
            ->first();

        $recipe->update([
            'rating_avg' => $stats->avg ?? null,
            'rating_count' => $stats->count ?? 0,
        ]);
    }
}
