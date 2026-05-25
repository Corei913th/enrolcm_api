<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use App\Services\Domain\Candidature\CandidatureCapabilitiesService;
use App\Services\Domain\Candidature\CandidatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly CandidatureService $candidatureService,
        private readonly CandidatureCapabilitiesService $capabilitiesService
    ) {}

    /**
     * Statistiques du tableau de bord candidat.
     *
     * @return JsonResponse Stats, alertes, échéances et candidatures récentes
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $candidat = $request->user()->candidat;
            $data = $this->candidatureService->getDashboardStats($candidat->utilisateur_id);

            return api_success($data, 'Statistiques du tableau de bord récupérées avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Vue d'ensemble des candidatures avec leurs capacités.
     * Endpoint d'agrégation pour le frontend - read-only.
     *
     * @return JsonResponse Candidatures avec capacités métier
     */
    public function overview(Request $request): JsonResponse
    {
        try {
            $candidat = $request->user()->candidat;

            // Récupérer les candidatures avec toutes les relations nécessaires
            $candidatures = $this->candidatureService->getCandidaturesByCandidat($candidat->utilisateur_id);

            $overview = $candidatures->map(function ($candidature) {
                // S'assurer que les relations sont chargées pour le service de capacités
                $candidature->load([
                    'documents.documentRequis',
                    'centreExamen',
                    'centreDepot',
                ]);

                return [
                    'candidature' => [
                        'id' => $candidature->id,
                        'concours_libelle' => $candidature->concours->libelle_concours ?? 'Concours',
                        'session_libelle' => $candidature->session->libelle_session ?? 'Session',
                        'statut' => $candidature->statut_candidature->value,
                        'code_candidat' => $candidature->code_cand_def ?? $candidature->code_cand_temp ?? 'N/A',
                        'logo_path' => $candidature->concours->ecole->logo_path ?? null,
                    ],
                    'capabilities' => $this->capabilitiesService->getCapabilities($candidature),
                ];
            });

            return api_success([
                'candidatures' => $overview->values()->all(),
                'total' => $candidatures->count(),
            ], 'Vue d\'ensemble récupérée avec succès');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }
}
