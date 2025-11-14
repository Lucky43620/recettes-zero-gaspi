<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Recipe;
use App\Services\RatingService;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function __construct(
        private RatingService $ratingService
    ) {}

    public function store(StoreRatingRequest $request, Recipe $recipe)
    {
        $this->ratingService->addOrUpdateRating(
            Auth::user(),
            $recipe,
            $request->validated('rating')
        );

        return back()->with('success', 'Votre note a été enregistrée');
    }

    public function destroy(Recipe $recipe)
    {
        $this->ratingService->removeRating(Auth::user(), $recipe);

        return back()->with('success', 'Votre note a été supprimée');
    }
}
