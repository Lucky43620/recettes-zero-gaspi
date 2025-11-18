<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_PREFIX = 'system_setting_';
    private const CACHE_DURATION = 3600;

    public function get(string $key, $default = null)
    {
        return Cache::remember(
            self::CACHE_PREFIX . $key,
            self::CACHE_DURATION,
            function () use ($key, $default) {
                return SystemSetting::getValue($key, $default);
            }
        );
    }

    public function set(string $key, $value, ?int $userId = null): bool
    {
        $result = SystemSetting::setValue($key, $value, $userId);

        if ($result) {
            Cache::forget(self::CACHE_PREFIX . $key);
            Cache::forget('system_settings_group_' . $this->getGroupByKey($key));
        }

        return $result;
    }

    public function getByGroup(string $group): array
    {
        return Cache::remember(
            'system_settings_group_' . $group,
            self::CACHE_DURATION,
            function () use ($group) {
                return SystemSetting::byGroup($group)
                    ->get()
                    ->mapWithKeys(function ($setting) {
                        return [$setting->key => $setting->getCastedValue()];
                    })
                    ->toArray();
            }
        );
    }

    public function updateMultiple(array $settings, ?int $userId = null): bool
    {
        $success = true;

        foreach ($settings as $key => $value) {
            if (!$this->set($key, $value, $userId)) {
                $success = false;
            }
        }

        return $success;
    }

    public function clearCache(?string $key = null): void
    {
        if ($key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        } else {
            Cache::flush();
        }
    }

    public function getAllSettings(): array
    {
        return SystemSetting::all()
            ->groupBy('group')
            ->map(function ($settings) {
                return $settings->mapWithKeys(function ($setting) {
                    return [$setting->key => [
                        'value' => $setting->getCastedValue(),
                        'type' => $setting->type,
                        'description' => $setting->description,
                    ]];
                })->toArray();
            })
            ->toArray();
    }

    private function getGroupByKey(string $key): string
    {
        $setting = SystemSetting::where('key', $key)->first();
        return $setting ? $setting->group : 'general';
    }
}
