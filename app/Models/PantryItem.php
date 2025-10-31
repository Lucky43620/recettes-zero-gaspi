<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PantryItem extends Model
{
    protected $fillable = [
        'user_id',
        'ingredient_id',
        'quantity',
        'unit_code',
        'expiration_date',
        'storage_location',
        'opened',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'opened' => 'boolean',
        'quantity' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_code', 'code');
    }

    public function isExpiringSoon(int $days = 3): bool
    {
        if (!$this->expiration_date) {
            return false;
        }

        return $this->expiration_date->diffInDays(Carbon::now(), false) <= $days
            && $this->expiration_date->isFuture();
    }

    public function isExpired(): bool
    {
        if (!$this->expiration_date) {
            return false;
        }

        return $this->expiration_date->isPast();
    }

    public function daysUntilExpiration(): ?int
    {
        if (!$this->expiration_date) {
            return null;
        }

        return (int) $this->expiration_date->diffInDays(Carbon::now(), false);
    }
}
