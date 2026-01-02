<?php

namespace App\Enums;

enum Mention: string
{
    case PASSABLE = 'PASSABLE';
    case ASSEZ_BIEN = 'ASSEZ_BIEN';
    case BIEN = 'BIEN';
    case TRES_BIEN = 'TRES_BIEN';
    case EXCELLENT = 'EXCELLENT';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PASSABLE => 'Passable',
            self::ASSEZ_BIEN => 'Assez Bien',
            self::BIEN => 'Bien',
            self::TRES_BIEN => 'Très Bien',
            self::EXCELLENT => 'Excellent',
        };
    }
}
