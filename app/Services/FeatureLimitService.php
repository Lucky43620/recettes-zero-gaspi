<?php

namespace App\Services;

use App\Models\User;

class FeatureLimitService
{
    protected $settings;

    // Map feature names to setting keys
    private const FEATURE_SETTING_MAP = [
        'pantry_items' => 'free_pantry_limit',
        'meal_plan_recipes' => 'free_meal_plan_limit',
        'collections' => 'free_collections_limit',
        'shopping_lists' => 'free_shopping_lists_limit',
    ];

    public function __construct(SettingsService $settings)
    {
        $this->settings = $settings;
    }

    public function canAdd(User $user, string $feature, int $currentCount): bool
    {
        if ($user->isPremium()) {
            return true;
        }

        $limit = $this->getFreeLimit($feature);

        return $currentCount < $limit;
    }

    public function getLimit(User $user, string $feature): int
    {
        if ($user->isPremium()) {
            return -1; // Unlimited for premium
        }

        return $this->getFreeLimit($feature);
    }

    public function getRemainingCount(User $user, string $feature, int $currentCount): int
    {
        if ($user->isPremium()) {
            return -1;
        }

        $limit = $this->getLimit($user, $feature);

        return max(0, $limit - $currentCount);
    }

    public function getLimitMessage(string $feature): string
    {
        $limit = $this->getFreeLimit($feature);

        return match($feature) {
            'pantry_items' => "Limite atteinte pour les utilisateurs gratuits ({$limit} produits max). Passez à Premium pour un garde-manger illimité !",
            'meal_plan_recipes' => "Limite atteinte pour les utilisateurs gratuits ({$limit} recettes max par semaine). Passez à Premium pour des plannings illimités !",
            'collections' => "Limite atteinte pour les utilisateurs gratuits ({$limit} collections max). Passez à Premium pour des collections illimitées !",
            'shopping_lists' => "Limite atteinte pour les utilisateurs gratuits ({$limit} listes max). Passez à Premium pour des listes illimitées !",
            default => "Limite atteinte. Passez à Premium pour plus de fonctionnalités !"
        };
    }

    /**
     * Get the free tier limit for a feature from SystemSettings
     */
    protected function getFreeLimit(string $feature): int
    {
        $settingKey = self::FEATURE_SETTING_MAP[$feature] ?? null;

        if (!$settingKey) {
            return 0;
        }

        return $this->settings->get($settingKey, 0);
    }
}
