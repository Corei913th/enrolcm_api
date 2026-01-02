<?php

namespace App\Enums;

enum StatutNote: string
{
    case EN_ATTENTE_SAISIE = 'EN_ATTENTE_SAISIE';
    case SAISIE_TERMINEE = 'SAISIE_TERMINEE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE_SAISIE => 'En attente de saisie',
            self::SAISIE_TERMINEE => 'Saisie terminée',
        };
    }
}
