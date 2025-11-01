<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Recipe;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

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

        return back()->with('success', 'Votre note a été enregistrée');
    }

    public function destroy(Recipe $recipe)
    {
        $deleted = Rating::where('user_id', Auth::id())
            ->where('recipe_id', $recipe->id)
            ->delete();

        if ($deleted) {
            $stats = $recipe->ratings()
                ->selectRaw('AVG(rating) as avg, COUNT(*) as count')
                ->first();

            $recipe->update([
                'rating_avg' => $stats->avg ?? 0,
                'rating_count' => $stats->count ?? 0,
            ]);
        }

        return back()->with('success', 'Votre note a été supprimée');
    }
}
