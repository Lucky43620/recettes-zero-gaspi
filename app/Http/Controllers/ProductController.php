<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductSearchRequest;
use App\Models\Ingredient;
use App\Services\IngredientService;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(
        private IngredientService $ingredientService
    ) {}

    public function index(ProductSearchRequest $request)
    {
        $query = $request->getQuery();
        $page = $request->getPage();
        $perPage = 24;
        $products = [];
        $hasMore = false;

        if ($query && strlen($query) >= 2) {
            $offset = ($page - 1) * $perPage;
            $limit = $perPage + 1;

            $results = $this->ingredientService->searchIngredients($query, $offset + $limit);

            $hasMore = $results->count() > ($offset + $perPage);

            $products = $results->skip($offset)->take($perPage)->map(function ($ingredient) {
                $data = $this->ingredientService->transformToArray($ingredient);
                $data['exists'] = $ingredient->exists ?? false;
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

