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
    ];

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
        ];
    }
}
