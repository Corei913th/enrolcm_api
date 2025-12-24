<?php

namespace App\Enums;

class DecisionAdmission
{
    public const ADMIS = 'ADMIS';
    public const LISTE_ATTENTE = 'LISTE_ATTENTE';
    public const REFUSEE = 'REFUSEE';

    public static function values(): array
    {
        return [
            self::ADMIS,
            self::LISTE_ATTENTE,
            self::REFUSEE,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::ADMIS => 'Admis',
            self::LISTE_ATTENTE => 'Liste d\'attente',
            self::REFUSEE => 'Refusée',
        ];

        return $labels[$value] ?? $value;
    }
}
