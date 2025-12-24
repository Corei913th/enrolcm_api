<?php

namespace App\Enums;

class Mention
{
    public const PASSABLE = 'PASSABLE';
    public const ASSEZ_BIEN = 'ASSEZ_BIEN';
    public const BIEN = 'BIEN';
    public const TRES_BIEN = 'TRES_BIEN';
    public const EXCELLENT = 'EXCELLENT';

    public static function values(): array
    {
        return [
            self::PASSABLE,
            self::ASSEZ_BIEN,
            self::BIEN,
            self::TRES_BIEN,
            self::EXCELLENT,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::PASSABLE => 'Passable',
            self::ASSEZ_BIEN => 'Assez Bien',
            self::BIEN => 'Bien',
            self::TRES_BIEN => 'Très Bien',
            self::EXCELLENT => 'Excellent',
        ];

        return $labels[$value] ?? $value;
    }
}
