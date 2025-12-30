<?php

namespace App\Enums;

enum EtatSession: string
{
    case OUVERTE = 'OUVERTE';
    case FERMEE = 'FERMEE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::OUVERTE => 'Ouverte',
            self::FERMEE => 'Fermée',
        };
    }
}
