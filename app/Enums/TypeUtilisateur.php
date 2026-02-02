<?php

namespace App\Enums;

enum TypeUtilisateur: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case ADMIN = 'ADMIN';
    case CANDIDAT = 'CANDIDAT';
    case RESPONSABLE_CENTRE = 'RESPONSABLE_CENTRE';
    case CORRECTEUR = 'CORRECTEUR';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrateur',
            self::ADMIN => 'Administrateur',
            self::CANDIDAT => 'Candidat',
            self::RESPONSABLE_CENTRE => 'Responsable de Centre',
            self::CORRECTEUR => 'Correcteur',
        };
    }
}
