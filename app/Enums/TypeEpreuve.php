<?php

namespace App\Enums;

class TypeEpreuve
{
    public const ECRIT = 'ECRIT';
    public const ORAL = 'ORAL';
    public const PRATIQUE = 'PRATIQUE';
    public const QCM = 'QCM';

    public static function values(): array
    {
        return [
            self::ECRIT,
            self::ORAL,
            self::PRATIQUE,
            self::QCM,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::ECRIT => 'Écrit',
            self::ORAL => 'Oral',
            self::PRATIQUE => 'Pratique',
            self::QCM => 'QCM',
        ];

        return $labels[$value] ?? $value;
    }
}
