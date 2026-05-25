<?php

namespace App\Enums;

enum StatutVerificationDocument: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case VALIDE = 'VALIDE';
    case REJETE = 'REJETE';
    case NON_SOUMIS = 'NON_SOUMIS';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::VALIDE => 'Validé',
            self::REJETE => 'Rejeté',
            self::NON_SOUMIS => 'Non soumis',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'warning',
            self::VALIDE => 'success',
            self::REJETE => 'danger',
            self::NON_SOUMIS => 'secondary',
        };
    }
}
