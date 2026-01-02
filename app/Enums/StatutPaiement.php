<?php

namespace App\Enums;

enum StatutPaiement: string
{
    case PENDING = 'PENDING';
    case VERIFIED = 'VERIFIED';
    case REJECTED = 'REJECTED';
    case OCR_VERIFIE = 'OCR_VERIFIE';
    case PENDING_MANUAL_REVIEW = 'PENDING_MANUAL_REVIEW';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::VERIFIED => 'Vérifié',
            self::REJECTED => 'Rejeté',
            self::OCR_VERIFIE => 'OCR vérifié',
            self::PENDING_MANUAL_REVIEW => 'Révision manuelle requise',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::VERIFIED => 'success',
            self::REJECTED => 'danger',
            self::OCR_VERIFIE => 'info',
            self::PENDING_MANUAL_REVIEW => 'secondary',
        };
    }
}
