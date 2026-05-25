<?php

namespace App\Enums;

enum Genre: string
{
    case M = 'M';
    case F = 'F';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::M => 'Masculin',
            self::F => 'Féminin',
        };
    }
}
