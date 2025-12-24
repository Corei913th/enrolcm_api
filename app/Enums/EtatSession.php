<?php

namespace App\Enums;

class EtatSession
{
    public const OUVERTE = 'OUVERTE';
    public const FERMEE = 'FERMEE';

    public static function values(): array
    {
        return [
            self::OUVERTE,
            self::FERMEE,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::OUVERTE => 'Ouverte',
            self::FERMEE => 'Fermée',
        ];

        return $labels[$value] ?? $value;
    }
}
