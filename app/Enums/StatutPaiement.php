<?php

namespace App\Enums;

enum StatutPaiement: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case OCR_VERIFIE = 'OCR_VERIFIE';
    case VALIDE = 'VALIDE';
    case REJETE = 'REJETE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::OCR_VERIFIE => 'OCR vérifié',
            self::VALIDE => 'Validé',
            self::REJETE => 'Rejeté',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'warning',
            self::OCR_VERIFIE => 'info',
            self::VALIDE => 'success',
            self::REJETE => 'danger',
        };
    }
}
