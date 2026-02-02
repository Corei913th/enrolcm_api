<?php

namespace App\Exceptions\Business;

use Exception;

class ResultatException extends Exception
{
    // Codes d'erreur pour le calcul des résultats
    public const AUCUNE_NOTE_TROUVEE = 'RESULTAT_001';
    public const NOTES_NON_VALIDEES = 'RESULTAT_002';
    public const CANDIDATURES_SANS_NOTES = 'RESULTAT_003';
    public const RESULTATS_DEJA_CALCULES = 'RESULTAT_004';
    public const EPREUVES_MANQUANTES = 'RESULTAT_005';

    // Codes d'erreur pour la détermination des admissions
    public const RESULTATS_NON_CALCULES = 'ADMISSION_001';
    public const PLACES_NON_DEFINIES = 'ADMISSION_002';
    public const ADMISSIONS_DEJA_DETERMINEES = 'ADMISSION_003';
    public const FILIERE_INTROUVABLE = 'ADMISSION_004';

    // Codes d'erreur pour la publication
    public const PUBLICATION_RESULTATS_NON_CALCULES = 'PUBLICATION_001';
    public const PUBLICATION_ADMISSIONS_NON_DETERMINEES = 'PUBLICATION_002';
    public const RESULTATS_DEJA_PUBLIES = 'PUBLICATION_003';
    public const CONCOURS_SESSION_INTROUVABLE = 'PUBLICATION_004';

    private string $errorCode;
    private string $userMessage;
    private string $severity;
    private array $details;
    private array $suggestedActions;

    public function __construct(
        string $errorCode,
        string $technicalMessage,
        string $userMessage,
        string $severity = 'error',
        array $details = [],
        array $suggestedActions = []
    ) {
        parent::__construct($technicalMessage);
        
        $this->errorCode = $errorCode;
        $this->userMessage = $userMessage;
        $this->severity = $severity;
        $this->details = $details;
        $this->suggestedActions = $suggestedActions;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function getSuggestedActions(): array
    {
        return $this->suggestedActions;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->errorCode,
            'message' => $this->getMessage(),
            'user_message' => $this->userMessage,
            'severity' => $this->severity,
            'details' => $this->details,
            'suggested_actions' => $this->suggestedActions,
        ];
    }



    public static function aucuneNoteTrouvee(string $concoursId, string $sessionId): self
    {
        return new self(
            self::AUCUNE_NOTE_TROUVEE,
            "Aucune note trouvée pour le concours {$concoursId} et la session {$sessionId}",
            "Aucune note n'a été saisie pour ce concours. Veuillez d'abord saisir les notes des candidats.",
            'error',
            ['concours_id' => $concoursId, 'session_id' => $sessionId],
            [
                'Accéder à la page de saisie des notes',
                'Saisir les notes des candidats',
                'Réessayer le calcul des résultats'
            ]
        );
    }

    public static function notesNonValidees(int $notesNonValidees, int $totalNotes): self
    {
        return new self(
            self::NOTES_NON_VALIDEES,
            "Des notes non validées ont été détectées ({$notesNonValidees}/{$totalNotes})",
            "Certaines notes n'ont pas encore été validées. Veuillez valider toutes les notes avant de calculer les résultats.",
            'error',
            [
                'notes_non_validees' => $notesNonValidees,
                'total_notes' => $totalNotes,
                'pourcentage_valide' => round((($totalNotes - $notesNonValidees) / $totalNotes) * 100, 2)
            ],
            [
                'Accéder à la page de validation des notes',
                'Valider les notes en attente',
                'Réessayer le calcul'
            ]
        );
    }

    public static function candidaturesSansNotes(int $candidaturesSansNotes, int $totalCandidatures): self
    {
        return new self(
            self::CANDIDATURES_SANS_NOTES,
            "Des candidatures sans notes complètes ont été détectées ({$candidaturesSansNotes}/{$totalCandidatures})",
            "Certains candidats n'ont pas toutes leurs notes. Veuillez compléter la saisie des notes avant de calculer les résultats.",
            'warning',
            [
                'candidatures_sans_notes' => $candidaturesSansNotes,
                'total_candidatures' => $totalCandidatures
            ],
            [
                'Vérifier la liste des candidats sans notes',
                'Compléter la saisie des notes manquantes',
                'Réessayer le calcul'
            ]
        );
    }

    public static function resultatsDejaCalcules(string $concoursId, string $sessionId): self
    {
        return new self(
            self::RESULTATS_DEJA_CALCULES,
            "Les résultats ont déjà été calculés pour ce concours/session",
            "Les résultats ont déjà été calculés. Si vous souhaitez recalculer, veuillez d'abord supprimer les résultats existants.",
            'warning',
            ['concours_id' => $concoursId, 'session_id' => $sessionId],
            [
                'Consulter les résultats existants',
                'Supprimer les résultats pour recalculer',
                'Publier les résultats'
            ]
        );
    }

    public static function epreuvesManquantes(array $epreuvesManquantes): self
    {
        return new self(
            self::EPREUVES_MANQUANTES,
            "Des épreuves n'ont pas de notes saisies",
            "Certaines épreuves n'ont pas encore de notes. Veuillez saisir les notes pour toutes les épreuves.",
            'error',
            ['epreuves_manquantes' => $epreuvesManquantes],
            [
                'Consulter la liste des épreuves',
                'Saisir les notes manquantes',
                'Réessayer le calcul'
            ]
        );
    }

    public static function resultatsNonCalcules(string $concoursId, string $sessionId): self
    {
        return new self(
            self::RESULTATS_NON_CALCULES,
            "Les résultats n'ont pas encore été calculés",
            "Vous devez d'abord calculer les résultats avant de déterminer les admissions.",
            'error',
            ['concours_id' => $concoursId, 'session_id' => $sessionId],
            [
                'Calculer les résultats',
                'Réessayer la détermination des admissions'
            ]
        );
    }

    public static function placesNonDefinies(string $filiereId): self
    {
        return new self(
            self::PLACES_NON_DEFINIES,
            "Le nombre de places n'est pas défini pour cette filière",
            "Le nombre de places disponibles n'est pas défini pour cette filière. Veuillez configurer le nombre de places.",
            'error',
            ['filiere_id' => $filiereId],
            [
                'Accéder à la configuration de la filière',
                'Définir le nombre de places',
                'Réessayer la détermination'
            ]
        );
    }

    public static function admissionsDejaDeterminees(string $concoursId, string $sessionId, string $filiereId): self
    {
        return new self(
            self::ADMISSIONS_DEJA_DETERMINEES,
            "Les admissions ont déjà été déterminées pour cette filière",
            "Les admissions ont déjà été déterminées. Si vous souhaitez redéterminer, veuillez d'abord réinitialiser les admissions.",
            'warning',
            [
                'concours_id' => $concoursId,
                'session_id' => $sessionId,
                'filiere_id' => $filiereId
            ],
            [
                'Consulter les admissions existantes',
                'Réinitialiser les admissions pour redéterminer',
                'Publier les résultats'
            ]
        );
    }

    public static function filiereIntrouvable(string $filiereId): self
    {
        return new self(
            self::FILIERE_INTROUVABLE,
            "Filière introuvable: {$filiereId}",
            "La filière spécifiée est introuvable. Veuillez vérifier l'identifiant de la filière.",
            'error',
            ['filiere_id' => $filiereId],
            [
                "Vérifier l'identifiant de la filière",
                "Consulter la liste des filières disponibles"
            ]
        );
    }

    public static function publicationResultatsNonCalcules(): self
    {
        return new self(
            self::PUBLICATION_RESULTATS_NON_CALCULES,
            "Impossible de publier : les résultats n'ont pas été calculés",
            "Vous devez d'abord calculer les résultats avant de les publier.",
            'error',
            [],
            [
                'Calculer les résultats',
                'Déterminer les admissions',
                'Réessayer la publication'
            ]
        );
    }

    public static function publicationAdmissionsNonDeterminees(): self
    {
        return new self(
            self::PUBLICATION_ADMISSIONS_NON_DETERMINEES,
            "Impossible de publier : les admissions n'ont pas été déterminées",
            "Vous devez d'abord déterminer les admissions avant de publier les résultats.",
            'error',
            [],
            [
                'Déterminer les admissions',
                'Réessayer la publication'
            ]
        );
    }

    public static function resultatsDejaPublies(string $concoursId, string $sessionId): self
    {
        return new self(
            self::RESULTATS_DEJA_PUBLIES,
            "Les résultats ont déjà été publiés",
            "Les résultats ont déjà été publiés. Vous ne pouvez pas les republier.",
            'warning',
            ['concours_id' => $concoursId, 'session_id' => $sessionId],
            [
                'Consulter les résultats publiés',
                'Dépublier pour modifier'
            ]
        );
    }

    public static function concoursSessionIntrouvable(string $concoursId, string $sessionId): self
    {
        return new self(
            self::CONCOURS_SESSION_INTROUVABLE,
            "Concours/session introuvable: {$concoursId}/{$sessionId}",
            "Le concours ou la session spécifiée est introuvable. Veuillez vérifier les identifiants.",
            'error',
            ['concours_id' => $concoursId, 'session_id' => $sessionId],
            [
                'Vérifier les identifiants',
                'Consulter la liste des concours et sessions'
            ]
        );
    }
}
