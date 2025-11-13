<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BarcodeController extends Controller
{
    public function lookup(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string|max:20',
        ]);

        $barcode = $request->input('barcode');

        try {
            $response = Http::timeout(10)
                ->get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json");

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé',
                ], 404);
            }

            $data = $response->json();

            if (!isset($data['product']) || $data['status'] === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé dans la base OpenFoodFacts',
                ], 404);
            }

            $product = $data['product'];

            $result = [
                'success' => true,
                'product' => [
                    'name' => $product['product_name'] ?? 'Produit sans nom',
                    'barcode' => $barcode,
                    'brand' => $product['brands'] ?? null,
                    'quantity' => $product['quantity'] ?? null,
                    'categories' => $product['categories'] ?? null,
                    'image_url' => $product['image_url'] ?? null,
                    'ingredients_text' => $product['ingredients_text_fr'] ?? $product['ingredients_text'] ?? null,
                    'nutriments' => [
                        'energy' => $product['nutriments']['energy-kcal_100g'] ?? null,
                        'fat' => $product['nutriments']['fat_100g'] ?? null,
                        'carbohydrates' => $product['nutriments']['carbohydrates_100g'] ?? null,
                        'proteins' => $product['nutriments']['proteins_100g'] ?? null,
                        'salt' => $product['nutriments']['salt_100g'] ?? null,
                    ],
                ],
            ];

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('OpenFoodFacts API error', [
                'barcode' => $barcode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche du produit',
            ], 500);
        }
    }
}
