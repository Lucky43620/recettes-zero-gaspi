<?php

namespace App\Enums;

enum MealType: string
{
    case BREAKFAST = 'breakfast';
    case LUNCH = 'lunch';
    case DINNER = 'dinner';
    case SNACK = 'snack';

    public function label(): string
    {
        return match($this) {
            self::BREAKFAST => 'Petit-déjeuner',
            self::LUNCH => 'Déjeuner',
            self::DINNER => 'Dîner',
            self::SNACK => 'Collation',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::BREAKFAST => '🌅',
            self::LUNCH => '☀️',
            self::DINNER => '🌙',
            self::SNACK => '🍪',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
