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
        // Déduit le groupe depuis le nom de la clé
        $group = static::inferGroupFromKey($key);

        $setting = static::firstOrCreate(
            ['key' => $key],
            ['group' => $group]
        );

        // Déterminer et sauvegarder le type de données
        if (is_array($value) || is_object($value)) {
            $setting->value = json_encode($value);
            $setting->type = 'json';
        } elseif (is_bool($value)) {
            $setting->value = $value ? '1' : '0';
            $setting->type = 'boolean';
        } elseif (is_int($value)) {
            $setting->value = (string)$value;
            $setting->type = 'integer';
        } elseif (is_float($value)) {
            $setting->value = (string)$value;
            $setting->type = 'float';
        } else {
            $setting->value = $value;
            $setting->type = 'string';
        }

        $setting->updated_by = $userId;
        return $setting->save();
    }

    protected static function inferGroupFromKey(string $key): string
    {
        // Map des préfixes vers les groupes
        $groupMappings = [
            'stripe_' => 'stripe',
            'free_' => 'limits',
            'monthly_' => 'stripe',
            'yearly_' => 'stripe',
            'trial_' => 'stripe',
            'site_' => 'general',
            'contact_' => 'general',
            'maintenance_' => 'general',
            'enable_' => 'features',
            'data_retention_' => 'gdpr',
            'dpo_' => 'gdpr',
            'terms_' => 'gdpr',
            'privacy_' => 'gdpr',
        ];

        // Cherche un préfixe correspondant
        foreach ($groupMappings as $prefix => $group) {
            if (str_starts_with($key, $prefix)) {
                return $group;
            }
        }

        // Si aucun préfixe ne correspond, retourne 'general' par défaut
        return 'general';
    }
}
