<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Services\FavoriteService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    public function __construct(
        private FavoriteService $favoriteService
    ) {}

    public function index()
    {
        $favorites = $this->favoriteService->getUserFavorites(Auth::user());

        return Inertia::render('Favorite/Index', [
            'favorites' => $favorites,
        ]);
    }

    public function toggle(Recipe $recipe)
    {
        $isFavorited = $this->favoriteService->toggle(Auth::user(), $recipe);

        $message = $isFavorited
            ? 'Recette ajoutée aux favoris'
            : 'Recette retirée des favoris';

        return back()->with('success', $message);
    }
}
