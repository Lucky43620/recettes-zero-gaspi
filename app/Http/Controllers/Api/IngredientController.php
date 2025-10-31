<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IngredientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function __construct(
        private IngredientService $ingredientService
    ) {}

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $results = $this->ingredientService->searchIngredients(
            $request->input('q'),
            $request->input('limit', 20)
        );

        return response()->json([
            'data' => $results,
            'count' => $results->count(),
        ]);
    }

    public function findByBarcode(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        $ingredient = $this->ingredientService->findOrCreateByBarcode(
            $request->input('barcode')
        );

        if (!$ingredient) {
            return response()->json([
                'message' => 'Produit non trouvé',
            ], 404);
        }

        return response()->json([
            'data' => $ingredient,
        ]);
    }

    public function findOrCreate(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|min:2',
        ]);

        $ingredient = $this->ingredientService->findOrCreateByName(
            $request->input('name')
        );

        return response()->json([
            'data' => $ingredient,
        ], $ingredient->wasRecentlyCreated ? 201 : 200);
    }
}
