<?php

namespace App\Enums;

enum CategorieAdmission: string
{
    case STANDARD = 'STANDARD';
    case CONDITIONNEL = 'CONDITIONNEL';
    case ELIMINATOIRE = 'ELIMINATOIRE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::STANDARD => 'Standard',
            self::CONDITIONNEL => 'Conditionnel',
            self::ELIMINATOIRE => 'Éliminatoire',
        };
    }
}
