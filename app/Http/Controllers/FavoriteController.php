<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favorites()
            ->with(['author', 'media'])
            ->paginate(12);

        return Inertia::render('Favorite/Index', [
            'favorites' => $favorites,
        ]);
    }

    public function toggle(Recipe $recipe)
    {
        $user = Auth::user();

        if ($user->hasFavorited($recipe)) {
            $user->favorites()->detach($recipe->id);
            return back()->with('success', 'Recette retirée des favoris');
        }

        $user->favorites()->attach($recipe->id);
        return back()->with('success', 'Recette ajoutée aux favoris');
    }
}
