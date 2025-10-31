<?php

namespace App\Integrations\OpenFoodFacts\DTOs;

class ProductData
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?string $brands,
        public readonly ?string $imageUrl,
        public readonly ?string $category,
        public readonly array $nutritionalInfo,
        public readonly array $allergens,
        public readonly array $labels,
    ) {}

    public static function fromOpenFoodFactsResponse(array $product): self
    {
        return new self(
            code: $product['code'] ?? '',
            name: $product['product_name'] ?? $product['product_name_fr'] ?? 'Produit inconnu',
            brands: $product['brands'] ?? null,
            imageUrl: $product['image_url'] ?? $product['image_front_url'] ?? null,
            category: $product['categories'] ?? null,
            nutritionalInfo: self::extractNutritionalInfo($product['nutriments'] ?? []),
            allergens: self::extractAllergens($product),
            labels: self::extractLabels($product['labels'] ?? ''),
        );
    }

    private static function extractNutritionalInfo(array $nutriments): array
    {
        return [
            'energy_kcal' => $nutriments['energy-kcal_100g'] ?? null,
            'energy_kj' => $nutriments['energy_100g'] ?? null,
            'fat' => $nutriments['fat_100g'] ?? null,
            'saturated_fat' => $nutriments['saturated-fat_100g'] ?? null,
            'carbohydrates' => $nutriments['carbohydrates_100g'] ?? null,
            'sugars' => $nutriments['sugars_100g'] ?? null,
            'fiber' => $nutriments['fiber_100g'] ?? null,
            'proteins' => $nutriments['proteins_100g'] ?? null,
            'salt' => $nutriments['salt_100g'] ?? null,
            'sodium' => $nutriments['sodium_100g'] ?? null,
        ];
    }

    private static function extractAllergens(array $product): array
    {
        $allergens = [];

        if (isset($product['allergens'])) {
            $allergens = array_filter(
                explode(',', $product['allergens']),
                fn($item) => !empty(trim($item))
            );
        }

        if (isset($product['allergens_tags'])) {
            $allergens = array_merge($allergens, $product['allergens_tags']);
        }

        return array_values(array_unique(array_map('trim', $allergens)));
    }

    private static function extractLabels(string $labels): array
    {
        if (empty($labels)) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $labels)),
            fn($item) => !empty($item)
        ));
    }

    public function toArray(): array
    {
        return [
            'openfoodfacts_id' => $this->code,
            'name' => $this->name,
            'brands' => $this->brands,
            'image_url' => $this->imageUrl,
            'category' => $this->category,
            'nutritional_info' => $this->nutritionalInfo,
            'allergens' => $this->allergens,
            'labels' => $this->labels,
            'openfoodfacts_synced_at' => now(),
        ];
    }
}
