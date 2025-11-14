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

    public function show($identifier)
    {
        if (is_numeric($identifier) && $identifier < 1000000) {
            $ingredient = Ingredient::findOrFail($identifier);
        } else {
            $ingredient = Ingredient::where('openfoodfacts_id', $identifier)->first();

            if (!$ingredient) {
                $ingredient = $this->ingredientService->findOrCreateByBarcode($identifier);

                if (!$ingredient) {
                    abort(404, 'Produit non trouvé');
                }
            }
        }

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
