<?php

namespace App\Enums;

enum StorageLocation: string
{
    case REFRIGERATOR = 'Réfrigérateur';
    case FREEZER = 'Congélateur';
    case CUPBOARD = 'Placard';
    case CELLAR = 'Cave';
    case PANTRY = 'Garde-manger';

    public function icon(): string
    {
        return match($this) {
            self::REFRIGERATOR => '❄️',
            self::FREEZER => '🧊',
            self::CUPBOARD => '🚪',
            self::CELLAR => '🏚️',
            self::PANTRY => '🥫',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->value, 'icon' => $case->icon()],
            self::cases()
        );
    }
}
