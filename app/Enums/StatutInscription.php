<?php

namespace App\Enums;

enum StatutInscription: string
{
    case BROUILLON = 'BROUILLON';
    case SUSPENDUE = 'SUSPENDUE';
    case CONFIRMEE = 'CONFIRMEE';
    case INVALIDEE = 'INVALIDEE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::BROUILLON => 'Brouillon',
            self::SUSPENDUE => 'Suspendue',
            self::CONFIRMEE => 'Confirmée',
            self::INVALIDEE => 'Invalidée',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::BROUILLON => 'secondary',
            self::SUSPENDUE => 'warning',
            self::CONFIRMEE => 'success',
            self::INVALIDEE => 'danger',
        };
    }
}
