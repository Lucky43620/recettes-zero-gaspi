<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'original_url' => $this->getUrl(),
            'conversions' => [
                'thumb' => $this->getUrl('thumb'),
                'medium' => $this->getUrl('medium'),
                'large' => $this->getUrl('large'),
            ],
        ];
    }
}
