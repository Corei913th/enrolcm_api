<?php

namespace App\Enums;

enum StatutVerificationPaiement: string
{
    case PENDING = 'PENDING';
    case VERIFIED = 'VERIFIED';
    case REJECTED = 'REJECTED';
    case OCR_VERIFIE = 'OCR_VERIFIE';
    case PENDING_MANUAL_REVIEW = 'PENDING_MANUAL_REVIEW';

    // Legacy cases for backward compatibility
    case EN_ATTENTE = 'en_attente';
    case VERIFIE = 'verifie';
    case REJETE = 'rejete';

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
            self::OCR_VERIFIE => 'OCR Vérifié',
            self::PENDING_MANUAL_REVIEW => 'En attente de validation manuelle',
            self::EN_ATTENTE => 'En attente',
            self::VERIFIE => 'Vérifié',
            self::REJETE => 'Rejeté',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::VERIFIED => 'success',
            self::REJECTED => 'danger',
            self::OCR_VERIFIE => 'info',
            self::PENDING_MANUAL_REVIEW => 'warning',
            self::EN_ATTENTE => 'warning',
            self::VERIFIE => 'success',
            self::REJETE => 'danger',
        };
    }
}
