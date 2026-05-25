<?php

namespace App\Enums;

enum StatutInscription: string
{
    case ACTIF = 'ACTIF';
    case INVALIDE = 'INVALIDE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIF => 'Actif',
            self::INVALIDE => 'Invalide',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIF => 'success',
            self::INVALIDE => 'danger',
        };
    }
}
