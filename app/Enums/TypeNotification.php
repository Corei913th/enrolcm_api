<?php

namespace App\Enums;

class TypeNotification
{
    // Candidature
    public const CANDIDATURE_SOUMISE = 'CANDIDATURE_SOUMISE';
    public const CANDIDATURE_VALIDEE = 'CANDIDATURE_VALIDEE';
    public const CANDIDATURE_REJETEE = 'CANDIDATURE_REJETEE';
    public const DOSSIER_INCOMPLET = 'DOSSIER_INCOMPLET';
    
    // Convocation
    public const CONVOCATION_DISPONIBLE = 'CONVOCATION_DISPONIBLE';
    public const RAPPEL_EXAMEN = 'RAPPEL_EXAMEN';
    
    // Résultats
    public const RESULTATS_DISPONIBLES = 'RESULTATS_DISPONIBLES';
    public const ADMISSION = 'ADMISSION';
    public const ECHEC = 'ECHEC';
    public const LISTE_ATTENTE = 'LISTE_ATTENTE';
    
    // Paiement
    public const PAIEMENT_RECU = 'PAIEMENT_RECU';
    public const PAIEMENT_VALIDE = 'PAIEMENT_VALIDE';
    public const PAIEMENT_REJETE = 'PAIEMENT_REJETE';
    
    // Système
    public const INFORMATION_GENERALE = 'INFORMATION_GENERALE';
    public const ALERTE = 'ALERTE';
    public const RAPPEL = 'RAPPEL';
    
    public static function values(): array
    {
        return [
            self::CANDIDATURE_SOUMISE,
            self::CANDIDATURE_VALIDEE,
            self::CANDIDATURE_REJETEE,
            self::DOSSIER_INCOMPLET,
            self::CONVOCATION_DISPONIBLE,
            self::RAPPEL_EXAMEN,
            self::RESULTATS_DISPONIBLES,
            self::ADMISSION,
            self::ECHEC,
            self::LISTE_ATTENTE,
            self::PAIEMENT_RECU,
            self::PAIEMENT_VALIDE,
            self::PAIEMENT_REJETE,
            self::INFORMATION_GENERALE,
            self::ALERTE,
            self::RAPPEL,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
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
            self::INFORMATION_GENERALE => 'Information générale',
            self::ALERTE => 'Alerte',
            self::RAPPEL => 'Rappel',
        ];

        return $labels[$value] ?? $value;
    }
}
