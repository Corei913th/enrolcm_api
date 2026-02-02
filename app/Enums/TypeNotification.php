<?php

namespace App\Enums;

enum TypeNotification: string
{
    // Candidature
    case CANDIDATURE_SOUMISE = 'CANDIDATURE_SOUMISE';
    case CANDIDATURE_VALIDEE = 'CANDIDATURE_VALIDEE';
    case CANDIDATURE_REJETEE = 'CANDIDATURE_REJETEE';
    case DOSSIER_INCOMPLET = 'DOSSIER_INCOMPLET';

        // Convocation
    case CONVOCATION_DISPONIBLE = 'CONVOCATION_DISPONIBLE';
    case RAPPEL_EXAMEN = 'RAPPEL_EXAMEN';

        // Résultats
    case RESULTATS_DISPONIBLES = 'RESULTATS_DISPONIBLES';
    case ADMISSION = 'ADMISSION';
    case ECHEC = 'ECHEC';
    case LISTE_ATTENTE = 'LISTE_ATTENTE';

        // Paiement
    case PAIEMENT_RECU = 'PAIEMENT_RECU';
    case PAIEMENT_VALIDE = 'PAIEMENT_VALIDE';
    case PAIEMENT_REJETE = 'PAIEMENT_REJETE';

        // Documents
    case DOCUMENT_VALIDE = 'DOCUMENT_VALIDE';
    case DOCUMENT_REJETE = 'DOCUMENT_REJETE';

        // Système
    case INFORMATION_GENERALE = 'INFORMATION_GENERALE';
    case ALERTE = 'ALERTE';
    case RAPPEL = 'RAPPEL';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::CANDIDATURE_SOUMISE => 'Candidature soumise',
            self::CANDIDATURE_VALIDEE => 'Candidature validée',
            self::CANDIDATURE_REJETEE => 'Candidature rejetée',
            self::DOSSIER_INCOMPLET => 'Dossier incomplet',
            self::CONVOCATION_DISPONIBLE => 'Convocation disponible',
            self::RAPPEL_EXAMEN => 'Rappel examen',
            self::RESULTATS_DISPONIBLES => 'Résultats disponibles',
            self::ADMISSION => 'Admission',
            self::ECHEC => 'Échec',
            self::LISTE_ATTENTE => 'Liste d\'attente',
            self::PAIEMENT_RECU => 'Paiement reçu',
            self::PAIEMENT_VALIDE => 'Paiement validé',
            self::PAIEMENT_REJETE => 'Paiement rejeté',
            self::DOCUMENT_VALIDE => 'Document validé',
            self::DOCUMENT_REJETE => 'Document rejeté',
            self::INFORMATION_GENERALE => 'Information générale',
            self::ALERTE => 'Alerte',
            self::RAPPEL => 'Rappel',
        };
    }
}
