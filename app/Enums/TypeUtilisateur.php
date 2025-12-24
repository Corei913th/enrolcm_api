<?php

namespace App\Enums;

class TypeUtilisateur
{
    public const ADMIN = 'ADMIN';
    public const CANDIDAT = 'CANDIDAT';
    public const RESPONSABLE_CENTRE = 'RESPONSABLE_CENTRE';
    public const CORRECTEUR = 'CORRECTEUR';

    public static function values(): array
    {
        return [
            self::ADMIN,
            self::CANDIDAT,
            self::RESPONSABLE_CENTRE,
            self::CORRECTEUR,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::ADMIN => 'Administrateur',
            self::CANDIDAT => 'Candidat',
            self::RESPONSABLE_CENTRE => 'Responsable de Centre',
            self::CORRECTEUR => 'Correcteur',
        ];

        return $labels[$value] ?? $value;
    }
}
