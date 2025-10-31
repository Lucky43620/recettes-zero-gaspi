<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Ingredient extends Model
{
    use Searchable;

    protected $fillable = [
        'name',
        'barcode',
        'category',
        'openfoodfacts_id',
        'nutritional_info',
        'allergens',
        'image_url',
        'labels',
        'brands',
        'avg_shelf_life_days',
        'openfoodfacts_synced_at',
    ];

    protected $casts = [
        'nutritional_info' => 'array',
        'allergens' => 'array',
        'labels' => 'array',
        'openfoodfacts_synced_at' => 'datetime',
    ];

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'brands' => $this->brands,
        ];
    }

    public function needsSync(): bool
    {
        if (!$this->openfoodfacts_synced_at) {
            return true;
        }

        return $this->openfoodfacts_synced_at->diffInDays(now()) > 30;
    }

    public function hasNutritionalInfo(): bool
    {
        return !empty($this->nutritional_info);
    }

    public function hasAllergens(): bool
    {
        return !empty($this->allergens);
    }
}
