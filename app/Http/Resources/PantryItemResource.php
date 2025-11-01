<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PantryItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ingredient' => [
                'id' => $this->ingredient->id,
                'name' => $this->ingredient->name,
                'image_url' => $this->ingredient->image_url,
            ],
            'quantity' => $this->quantity,
            'unit' => [
                'code' => $this->unit->code,
                'name' => $this->unit->name,
            ],
            'expiration_date' => $this->expiration_date?->format('Y-m-d'),
            'storage_location' => $this->storage_location,
            'opened' => $this->opened,
            'is_expired' => $this->isExpired(),
            'is_expiring_soon' => $this->isExpiringSoon(),
            'days_until_expiration' => $this->daysUntilExpiration(),
        ];
    }
}
