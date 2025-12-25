<?php

namespace App\Enums;

enum StatutVerificationPaiement: string
{
    case EN_ATTENTE = 'en_attente';
    case VERIFIE = 'verifie';
    case REJETE = 'rejete';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente de vérification',
            self::VERIFIE => 'Vérifié',
            self::REJETE => 'Rejeté',
        };
    }
}
