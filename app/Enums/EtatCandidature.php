<?php

namespace App\Enums;

class EtatCandidature
{
    public const EN_ATTENTE = 'EN_ATTENTE';
    public const EN_COURS = 'EN_COURS';
    public const APPROUVEE = 'APPROUVEE';
    public const REJETTEE = 'REJETTEE';

    public static function values(): array
    {
        return [
            self::EN_ATTENTE,
            self::EN_COURS,
            self::APPROUVEE,
            self::REJETTEE,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::EN_ATTENTE => 'En attente',
            self::EN_COURS => 'En cours',
            self::APPROUVEE => 'Approuvée',
            self::REJETTEE => 'Rejetée',
        ];

        return $labels[$value] ?? $value;
    }
}
