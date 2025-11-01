<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'code';
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'label',
    ];

    public function pantryItems(): HasMany
    {
        return $this->hasMany(PantryItem::class, 'unit_code', 'code');
    }

    public function shoppingListItems(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class, 'unit_code', 'code');
    }
}
