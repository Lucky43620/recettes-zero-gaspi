<?php

namespace App\Services;

use App\Models\User;

class FeatureLimitService
{
    private const LIMITS = [
        'pantry_items' => [
            'free' => 10,
            'premium' => -1,
        ],
        'meal_plan_recipes' => [
            'free' => 3,
            'premium' => -1,
        ],
        'collections' => [
            'free' => 3,
            'premium' => -1,
        ],
        'shopping_lists' => [
            'free' => 2,
            'premium' => -1,
        ],
    ];

    public function canAdd(User $user, string $feature, int $currentCount): bool
    {
        if ($user->isPremium()) {
            return true;
        }

        $limit = self::LIMITS[$feature]['free'] ?? 0;

        return $currentCount < $limit;
    }

    public function getLimit(User $user, string $feature): int
    {
        if ($user->isPremium()) {
            return self::LIMITS[$feature]['premium'];
        }

        return self::LIMITS[$feature]['free'] ?? 0;
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
        $limit = self::LIMITS[$feature]['free'] ?? 0;

        return match($feature) {
            'pantry_items' => "Limite atteinte pour les utilisateurs gratuits ({$limit} produits max). Passez à Premium pour un garde-manger illimité !",
            'meal_plan_recipes' => "Limite atteinte pour les utilisateurs gratuits ({$limit} recettes max par semaine). Passez à Premium pour des plannings illimités !",
            'collections' => "Limite atteinte pour les utilisateurs gratuits ({$limit} collections max). Passez à Premium pour des collections illimitées !",
            'shopping_lists' => "Limite atteinte pour les utilisateurs gratuits ({$limit} listes max). Passez à Premium pour des listes illimitées !",
            default => "Limite atteinte. Passez à Premium pour plus de fonctionnalités !"
        };
    }
}
