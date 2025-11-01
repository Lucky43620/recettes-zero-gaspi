<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Inertia\Inertia;

class IngredientController extends Controller
{
    public function show(Ingredient $ingredient)
    {
        return Inertia::render('Ingredient/Show', [
            'ingredient' => $ingredient,
        ]);
    }
}
