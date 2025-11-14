<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarcodeLookupRequest;
use App\Models\Ingredient;
use App\Services\IngredientService;
use Inertia\Inertia;

class IngredientController extends Controller
{
    public function __construct(
        private IngredientService $ingredientService
    ) {}

    public function show(Ingredient $ingredient)
    {
        return Inertia::render('Ingredient/Show', [
            'ingredient' => $ingredient,
        ]);
    }

    public function lookupBarcode(BarcodeLookupRequest $request)
    {
        $barcode = $request->getBarcode();
        $productData = $this->ingredientService->lookupBarcode($barcode);

        if (!$productData) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé dans la base OpenFoodFacts',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => $productData,
        ]);
    }
}
