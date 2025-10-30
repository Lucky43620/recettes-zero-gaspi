<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Recipe;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Recipe $recipe)
    {
        $validated = $request->validated();

        Rating::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'recipe_id' => $recipe->id,
            ],
            [
                'rating' => $validated['rating'],
                'review' => $validated['review'] ?? null,
            ]
        );

        $this->updateRecipeRating($recipe);

        return back()->with('success', 'Votre note a été enregistrée');
    }

    public function destroy(Recipe $recipe)
    {
        Rating::where('user_id', Auth::id())
            ->where('recipe_id', $recipe->id)
            ->delete();

        $this->updateRecipeRating($recipe);

        return back()->with('success', 'Votre note a été supprimée');
    }

    private function updateRecipeRating(Recipe $recipe)
    {
        $stats = $recipe->ratings()
            ->select(DB::raw('AVG(rating) as avg, COUNT(*) as count'))
            ->first();

        $recipe->update([
            'rating_avg' => $stats->avg ?? 0,
            'rating_count' => $stats->count ?? 0,
        ]);
    }
}
