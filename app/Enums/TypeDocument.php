<?php

namespace App\Enums;

class TypeDocument
{
    public const FICHE_PAIEMENT = 'FICHE_PAIEMENT';
    public const CNI = 'CNI';
    public const ACTE_NAISSANCE = 'ACTE_NAISSANCE';
    public const RELEVE_NOTE = 'RELEVE_NOTE';
    public const CERTIFICAT_NATIONALITE = 'CERTIFICAT_NATIONALITE';
    public const CERTIFICAT_MEDICAL = 'CERTIFICAT_MEDICAL';
    public const PHOTO_IDENTITE = 'PHOTO_IDENTITE';
    public const ATTESTATION_BAC = 'ATTESTATION_BAC';
    public const DIPLOME = 'DIPLOME';

    public static function values(): array
    {
        return [
            self::FICHE_PAIEMENT,
            self::CNI,
            self::ACTE_NAISSANCE,
            self::RELEVE_NOTE,
            self::CERTIFICAT_NATIONALITE,
            self::CERTIFICAT_MEDICAL,
            self::PHOTO_IDENTITE,
            self::ATTESTATION_BAC,
            self::DIPLOME,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::FICHE_PAIEMENT => 'Fiche de paiement',
            self::CNI => 'Carte Nationale d\ identité',
            self::ACTE_NAISSANCE => 'Acte de naissance',
            self::RELEVE_NOTE => 'Relevé de notes',
            self::CERTIFICAT_NATIONALITE => 'Certificat de nationalité',
            self::CERTIFICAT_MEDICAL => 'Certificat médical',
            self::PHOTO_IDENTITE => 'Photo d\identité',
            self::ATTESTATION_BAC => 'Attestation de bac',
            self::DIPLOME => 'Diplôme',
        ];

        return $labels[$value] ?? $value;
    }
}
