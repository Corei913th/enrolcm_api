<?php

namespace App\Enums;

enum TypeDocument: string
{
    case CNI = 'CNI';
    case ACTE_NAISSANCE = 'ACTE_NAISSANCE';
    case RELEVE_NOTE = 'RELEVE_NOTE';
    case CERTIFICAT_NATIONALITE = 'CERTIFICAT_NATIONALITE';
    case CERTIFICAT_MEDICAL = 'CERTIFICAT_MEDICAL';
    case PHOTO_IDENTITE = 'PHOTO_IDENTITE';
    case ATTESTATION_BAC = 'ATTESTATION_BAC';
    case DIPLOME = 'DIPLOME';
    case CERTIFICAT_SCOLARITE = 'CERTIFICAT_SCOLARITE';
    case ATTESTATION_REUSSITE = 'ATTESTATION_REUSSITE';
    case CASIER_JUDICIAIRE = 'CASIER_JUDICIAIRE';
    case CERTIFICAT_RESIDENCE = 'CERTIFICAT_RESIDENCE';
    case AUTRE = 'AUTRE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
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
        };
    }
}
