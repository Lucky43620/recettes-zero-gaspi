<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
        'updated_by',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    public function getCastedValue()
    {
        return match($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'number' => (int) $this->value,
            'float', 'decimal' => (float) $this->value,
            'json', 'array' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->getCastedValue() : $default;
    }

    public static function setValue(string $key, $value, ?int $userId = null): bool
    {
        $setting = static::firstOrCreate(['key' => $key]);

        if (is_array($value) || is_object($value)) {
            $setting->value = json_encode($value);
            $setting->type = 'json';
        } else {
            $setting->value = $value;
        }

        $setting->updated_by = $userId;
        return $setting->save();
    }
}
