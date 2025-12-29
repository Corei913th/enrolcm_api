<?php

namespace App\Enums;

enum StatutPaiement: string
{
    case PENDING = 'PENDING';
    case VERIFIED = 'VERIFIED';
    case REJECTED = 'REJECTED';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::VERIFIED => 'Vérifié',
            self::REJECTED => 'Rejeté',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::VERIFIED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
