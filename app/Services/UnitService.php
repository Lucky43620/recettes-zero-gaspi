<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Support\Facades\Cache;

class UnitService
{
    private const CACHE_KEY = 'units:all';
    private const CACHE_TTL = 86400 * 30;

    public function getAllUnits()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Unit::all();
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
