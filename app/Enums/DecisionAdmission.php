<?php

namespace App\Enums;

enum DecisionAdmission: string
{
    case ADMIS = 'ADMIS';
    case LISTE_ATTENTE = 'LISTE_ATTENTE';
    case REFUSEE = 'REFUSEE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIS => 'Admis',
            self::LISTE_ATTENTE => 'Liste d\'attente',
            self::REFUSEE => 'Refusée',
        };
    }
}
