<?php

namespace App\Http\Controllers\Concours;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resultats\CalculerResultatsRequest;
use App\Http\Requests\Resultats\DeterminerAdmissionsRequest;
use App\Models\ResultatPublication;
use App\Services\Domain\Examen\ResultatService;
use App\Services\Infrastructure\Pdf\ResultatsPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResultatController extends Controller
{
    public function __construct(
        private readonly ResultatService $resultatService,
        private readonly ResultatsPdfService $resultatsPdfService
    ) {}

    /**
     * Liste des résultats pour un concours/session.
     */
    public function getResultats(string $concoursId, string $sessionId, Request $request): JsonResponse
    {
        $filiereId = $request->query('filiere_id');
        $perPage = $request->query('per_page', 100); // Increased from 15 to 100

        $resultats = $this->resultatService->getResultats($concoursId, $sessionId, $filiereId, $perPage);

        return api_paginated($resultats, 'Résultats récupérés avec succès');
    }

    /**
     * Calculer les résultats (Moyennes).
     */
    public function calculerResultats(string $concoursId, string $sessionId, CalculerResultatsRequest $request): JsonResponse
    {
        $force = $request->input('force', false);
        $result = $this->resultatService->calculerResultats($concoursId, $sessionId, $force);

        return api_success($result, 'Calcul des résultats terminé');
    }

    /**
     * Déterminer les admissions (Classement).
     */
    public function determinerAdmissions(string $concoursId, string $sessionId, DeterminerAdmissionsRequest $request): JsonResponse
    {
        $filiereId = $request->input('filiere_id');
        $force = $request->input('force', false);
        $maxParRegion = $request->input('max_par_region') ?? [];

        $result = $this->resultatService->determinerAdmissions($concoursId, $sessionId, $filiereId, $force, $maxParRegion);

        return api_success($result, 'Détermination des admissions terminée');
    }

    /**
     * Déterminer les admissions pour TOUTES les filières.
     */
    public function determinerToutesAdmissions(string $concoursId, string $sessionId, Request $request): JsonResponse
    {
        $force = $request->input('force', false);
        $result = $this->resultatService->determinerToutesAdmissions($concoursId, $sessionId, $force);

        return api_success($result, 'Détermination globale des admissions terminée');
    }

    /**
     * Traitement global (Calcul + Admissions).
     */
    public function traiterGlobalement(string $concoursId, string $sessionId, Request $request): JsonResponse
    {
        $force = $request->input('force', false);
        $result = $this->resultatService->traiterResultatsGlobaux($concoursId, $sessionId, $force);

        return api_success($result, 'Traitement global terminé');
    }

    /**
     * Publier les résultats officiellement.
     */
    public function publierResultats(string $concoursId, string $sessionId, Request $request): JsonResponse
    {
        $datePrevue = $request->input('date_publication_prevue');
        $message = $request->input('message_candidat');
        $timerActif = $request->input('timer_actif', false);

        $result = $this->resultatService->publierResultats(
            $concoursId,
            $sessionId,
            $datePrevue,
            $message,
            $timerActif
        );

        return api_success($result['data'], $result['message']);
    }

    /**
     * Dépublier les résultats.
     */
    public function depublierResultats(string $concoursId, string $sessionId): JsonResponse
    {
        $result = $this->resultatService->depublierResultats($concoursId, $sessionId);

        return api_success($result['data'], $result['message']);
    }

    /**
     * Obtenir la date de publication prévue pour le timer
     */
    public function getDatePublication(string $concoursId, string $sessionId): JsonResponse
    {
        try {
            $publication = ResultatPublication::where('concours_id', $concoursId)
                ->where('session_id', $sessionId)
                ->first();

            if (! $publication) {
                return api_success([
                    'est_publie' => false,
                    'timer_actif' => false,
                    'message' => 'Aucune publication configurée',
                ]);
            }

            return api_success([
                'est_publie' => $publication->est_publie,
                'date_publication_prevue' => $publication->date_publication_prevue?->toDateTimeString(),
                'date_publication_effective' => $publication->date_publication_effective?->toDateTimeString(),
                'timer_actif' => $publication->timer_actif,
                'message_candidat' => $publication->message_candidat,
                'temps_restant' => $publication->getTempsRestant(),
                'temps_restant_format' => $publication->getTempsRestantFormat(),
                'message' => $publication->est_publie ? 'Résultats publiés' : ($publication->timer_actif ? 'En attente de publication' : 'Non publié'),
            ]);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Obtenir le résultat individuel d'un candidat.
     */
    public function getResultatCandidat(string $concoursId, string $sessionId, string $candidatureId, Request $request): JsonResponse
    {
        $resultat = $this->resultatService->getResultatCandidat($candidatureId);

        if (! $resultat) {
            return api_not_found('Résultat introuvable pour ce candidat');
        }

        // Vérifier si publié ou si admin
        if (! $resultat->date_publication && ! $request->user()->isAdmin()) {
            return api_forbidden('Les résultats n\'ont pas encore été publiés');
        }

        return api_success($resultat);
    }

    /**
     * Formater le temps restant pour l'affichage
     */
    private function formatTempsRestant($datePublication): string
    {
        if (! $datePublication) {
            return 'Non défini';
        }

        if ($datePublication->isPast()) {
            return 'Publié';
        }

        $seconds = $datePublication->diffInSeconds(now());
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $days = floor($hours / 24);
        $hours = $hours % 24;

        if ($days > 0) {
            return "{$days}j {$hours}h {$minutes}min";
        }

        return "{$hours}h {$minutes}min";
    }

    /**
     * Obtenir le classement pour une filière.
     */
    public function getClassementFiliere(string $filiereId, Request $request): JsonResponse
    {
        $concoursId = $request->query('concours_id');
        $sessionId = $request->query('session_id');
        $perPage = $request->input('per_page', 50);

        if (! $concoursId || ! $sessionId) {
            return api_error('Paramètres requis: concours_id et session_id', null, 422);
        }

        $resultats = $this->resultatService->getClassement($concoursId, $sessionId, $filiereId, $perPage);

        return api_paginated($resultats, 'Classement de la filière');
    }

    /**
     * Télécharger la fiche des résultats en PDF.
     *
     * GET /api/v1/admin/concours/{concours}/sessions/{session}/resultats/pdf
     */
    public function telechargerFicheResultats(string $concoursId, string $sessionId)
    {
        try {
            $pdf = $this->resultatsPdfService->genererFicheResultats($concoursId, $sessionId);

            $filename = 'resultats_' . $concoursId . '_' . date('Y-m-d') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Télécharger la fiche des résultats par filière en PDF.
     *
     * GET /api/v1/admin/concours/{concours}/sessions/{session}/filieres/{filiere}/resultats/pdf
     */
    public function telechargerFicheResultatsParFiliere(
        string $concoursId,
        string $sessionId,
        string $filiereId
    ) {
        try {
            $pdf = $this->resultatsPdfService->genererFicheResultatsParFiliere(
                $concoursId,
                $filiereId,
                $sessionId
            );

            $filename = 'resultats_filiere_' . $filiereId . '_' . date('Y-m-d') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }
}
