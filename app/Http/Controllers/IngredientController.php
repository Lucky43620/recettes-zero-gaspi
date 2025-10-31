<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Inertia\Inertia;

class IngredientController extends Controller
{
    public function show(Ingredient $ingredient)
    {
        $ingredient->load([]);

        return Inertia::render('Ingredient/Show', [
            'ingredient' => $ingredient,
        ]);
    }
}
