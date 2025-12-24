<?php

namespace App\Enums;

class StatutNote
{
    public const EN_ATTENTE_SAISIE = 'EN_ATTENTE_SAISIE';
    public const SAISIE_TERMINEE = 'SAISIE_TERMINEE';

    public static function values(): array
    {
        return [
            self::EN_ATTENTE_SAISIE,
            self::SAISIE_TERMINEE,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::EN_ATTENTE_SAISIE => 'En attente de saisie',
            self::SAISIE_TERMINEE => 'Saisie terminée',
        ];

        return $labels[$value] ?? $value;
    }
}
