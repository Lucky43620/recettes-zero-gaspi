<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Services\IngredientService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(
        private IngredientService $ingredientService
    ) {}

    public function index(Request $request)
    {
        $query = $request->input('q');
        $products = [];

        if ($query && strlen($query) >= 2) {
            $results = $this->ingredientService->searchIngredients($query);

            $products = collect($results)->map(function ($ingredient) {
                return [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                    'barcode' => $ingredient->barcode,
                    'brands' => $ingredient->brands,
                    'category' => $ingredient->category,
                    'image_url' => $ingredient->image_url,
                    'nutritional_info' => $ingredient->nutritional_info,
                    'labels' => $ingredient->labels,
                ];
            })->values()->all();
        }

        return Inertia::render('Products/Index', [
            'products' => $products,
            'query' => $query,
        ]);
    }
}
