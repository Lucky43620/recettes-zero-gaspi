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
        $page = (int) $request->input('page', 1);
        $perPage = 20;
        $products = [];
        $hasMore = false;

        if ($query && strlen($query) >= 2) {
            $limit = $perPage * $page;
            $results = $this->ingredientService->searchIngredients($query, $limit + 1);

            $hasMore = $results->count() > $limit;

            $products = $results->take($limit)->map(function ($ingredient) {
                if (!$ingredient->exists) {
                    $ingredient = $this->ingredientService->findOrCreateByName($ingredient->name);
                }

                $data = $this->ingredientService->transformToArray($ingredient);
                $data['exists'] = true;
                return $data;
            })->values()->all();
        }

        return Inertia::render('Products/Index', [
            'products' => $products,
            'query' => $query,
            'currentPage' => $page,
            'hasMore' => $hasMore,
        ]);
    }
}
