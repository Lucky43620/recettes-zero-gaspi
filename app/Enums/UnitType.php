<?php

namespace App\Enums;

enum UnitType: string
{
    case WEIGHT = 'weight';
    case VOLUME = 'volume';
    case PIECE = 'piece';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::WEIGHT => 'Poids',
            self::VOLUME => 'Volume',
            self::PIECE => 'Pièce',
            self::OTHER => 'Autre',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
