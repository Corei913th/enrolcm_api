<?php

namespace App\Enums;

enum TypeEpreuve: string
{
    case ECRIT = 'ECRIT';
    case ORAL = 'ORAL';
    case PRATIQUE = 'PRATIQUE';
    case QCM = 'QCM';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ECRIT => 'Écrit',
            self::ORAL => 'Oral',
            self::PRATIQUE => 'Pratique',
            self::QCM => 'QCM',
        };
    }
}
