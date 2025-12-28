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
    public const CERTIFICAT_SCOLARITE = 'CERTIFICAT_SCOLARITE';
    public const ATTESTATION_REUSSITE = 'ATTESTATION_REUSSITE';
    public const CASIER_JUDICIAIRE = 'CASIER_JUDICIAIRE';
    public const CERTIFICAT_RESIDENCE = 'CERTIFICAT_RESIDENCE';
    public const AUTRE = 'AUTRE';

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
            self::CERTIFICAT_SCOLARITE,
            self::ATTESTATION_REUSSITE,
            self::CASIER_JUDICIAIRE,
            self::CERTIFICAT_RESIDENCE,
            self::AUTRE,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::FICHE_PAIEMENT => 'Fiche de paiement',
            self::CNI => 'Carte Nationale d\'identité',
            self::ACTE_NAISSANCE => 'Acte de naissance',
            self::RELEVE_NOTE => 'Relevé de notes',
            self::CERTIFICAT_NATIONALITE => 'Certificat de nationalité',
            self::CERTIFICAT_MEDICAL => 'Certificat médical',
            self::PHOTO_IDENTITE => 'Photo d\'identité',
            self::ATTESTATION_BAC => 'Attestation de baccalauréat',
            self::DIPLOME => 'Diplôme',
            self::CERTIFICAT_SCOLARITE => 'Certificat de scolarité',
            self::ATTESTATION_REUSSITE => 'Attestation de réussite',
            self::CASIER_JUDICIAIRE => 'Casier judiciaire',
            self::CERTIFICAT_RESIDENCE => 'Certificat de résidence',
            self::AUTRE => 'Autre document',
        ];

        return $labels[$value] ?? $value;
    }
}
