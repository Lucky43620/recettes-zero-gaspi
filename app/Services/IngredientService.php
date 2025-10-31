<?php

namespace App\Services;

use App\Integrations\OpenFoodFacts\DTOs\ProductData;
use App\Integrations\OpenFoodFacts\OpenFoodFactsConnector;
use App\Integrations\OpenFoodFacts\Requests\GetProductByBarcodeRequest;
use App\Integrations\OpenFoodFacts\Requests\SearchProductsRequest;
use App\Models\Ingredient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class IngredientService
{
    public function __construct(
        private OpenFoodFactsConnector $connector
    ) {}

    public function searchIngredients(string $searchTerm, int $limit = 20, bool $localOnly = false): Collection
    {
        $localResults = $this->searchLocal($searchTerm, $limit);

        if ($localOnly || $localResults->count() >= $limit) {
            return $localResults;
        }

        if ($localResults->count() >= 5) {
            return $localResults;
        }

        $apiResults = $this->searchFromOpenFoodFacts($searchTerm, $limit);

        return $localResults->concat($apiResults)->unique('id')->take($limit);
    }

    public function findOrCreateByBarcode(string $barcode): ?Ingredient
    {
        $ingredient = Ingredient::where('barcode', $barcode)->first();

        if ($ingredient) {
            if ($ingredient->needsSync()) {
                $this->syncWithOpenFoodFacts($ingredient);
            }
            return $ingredient;
        }

        return $this->createFromBarcode($barcode);
    }

    public function findOrCreateByName(string $name): Ingredient
    {
        $ingredient = Ingredient::where('name', $name)->first();

        if ($ingredient) {
            return $ingredient;
        }

        $productData = $this->searchProductByName($name);

        if ($productData) {
            return $this->createFromProductData($productData);
        }

        return Ingredient::create(['name' => $name]);
    }

    private function searchLocal(string $searchTerm, int $limit): Collection
    {
        $query = Ingredient::query();

        if (strlen($searchTerm) >= 3) {
            $query->whereRaw('MATCH(name) AGAINST(? IN BOOLEAN MODE)', [$searchTerm . '*'])
                ->orWhere('brands', 'like', "%{$searchTerm}%")
                ->orWhere('barcode', 'like', "%{$searchTerm}%");
        } else {
            $query->where('name', 'like', "{$searchTerm}%")
                ->orWhere('brands', 'like', "{$searchTerm}%")
                ->orWhere('barcode', 'like', "{$searchTerm}%");
        }

        return $query->limit($limit)->get();
    }

    private function searchFromOpenFoodFacts(string $searchTerm, int $pageSize = 20): Collection
    {
        try {
            $request = new SearchProductsRequest($searchTerm, 1, $pageSize);
            $response = $this->connector->send($request);

            if (!$response->successful()) {
                Log::warning('OpenFoodFacts search failed', [
                    'search_term' => $searchTerm,
                    'status' => $response->status(),
                ]);
                return collect();
            }

            $data = $response->json();
            $products = $data['products'] ?? [];

            return collect($products)->map(function ($product) {
                $productData = ProductData::fromOpenFoodFactsResponse($product);
                return $this->createFromProductData($productData);
            });
        } catch (\Exception $e) {
            Log::error('OpenFoodFacts search error', [
                'search_term' => $searchTerm,
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    private function createFromBarcode(string $barcode): ?Ingredient
    {
        try {
            $request = new GetProductByBarcodeRequest($barcode);
            $response = $this->connector->send($request);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if (($data['status'] ?? 0) !== 1 || !isset($data['product'])) {
                return null;
            }

            $productData = ProductData::fromOpenFoodFactsResponse($data['product']);
            return $this->createFromProductData($productData, $barcode);
        } catch (\Exception $e) {
            Log::error('OpenFoodFacts barcode lookup error', [
                'barcode' => $barcode,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function searchProductByName(string $name): ?ProductData
    {
        try {
            $request = new SearchProductsRequest($name, 1, 1);
            $response = $this->connector->send($request);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $products = $data['products'] ?? [];

            if (empty($products)) {
                return null;
            }

            return ProductData::fromOpenFoodFactsResponse($products[0]);
        } catch (\Exception $e) {
            Log::error('OpenFoodFacts product search error', [
                'name' => $name,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function createFromProductData(ProductData $productData, ?string $barcode = null): Ingredient
    {
        $data = $productData->toArray();

        if ($barcode) {
            $data['barcode'] = $barcode;
        }

        $existing = Ingredient::where('openfoodfacts_id', $productData->code)->first();

        if ($existing) {
            $existing->update($data);
            return $existing;
        }

        return Ingredient::create($data);
    }

    private function syncWithOpenFoodFacts(Ingredient $ingredient): void
    {
        if (!$ingredient->barcode && !$ingredient->openfoodfacts_id) {
            return;
        }

        try {
            $barcode = $ingredient->barcode ?? $ingredient->openfoodfacts_id;
            $request = new GetProductByBarcodeRequest($barcode);
            $response = $this->connector->send($request);

            if (!$response->successful()) {
                return;
            }

            $data = $response->json();

            if (($data['status'] ?? 0) !== 1 || !isset($data['product'])) {
                return;
            }

            $productData = ProductData::fromOpenFoodFactsResponse($data['product']);
            $ingredient->update($productData->toArray());
        } catch (\Exception $e) {
            Log::error('OpenFoodFacts sync error', [
                'ingredient_id' => $ingredient->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
