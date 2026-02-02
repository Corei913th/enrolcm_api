<?php

namespace App\Services\Domain\Examen;

use App\Exceptions\Business\ResultatException;
use App\Models\AdmissionRule;
use App\Services\Domain\Examen\Validators\ResultatValidator;
use App\Services\Domain\Examen\Repositories\ResultatRepository;
use App\Services\Domain\Examen\Processors\ResultatProcessor;
use App\Services\Domain\Examen\Processors\AdmissionProcessor;
use App\Services\Domain\Examen\Processors\IntelligentAdmissionProcessor;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ResultatService
{
    public function __construct(
        private readonly ResultatValidator $validator,
        private readonly ResultatRepository $repository,
        private readonly ResultatProcessor $resultatProcessor,
        private readonly AdmissionProcessor $admissionProcessor,
        private readonly IntelligentAdmissionProcessor $intelligentAdmissionProcessor,
        private readonly ActivityLoggerService $logger
    ) {}

    /**
     * Calculer les résultats pour un concours/session
     */
    public function calculerResultats(string $concoursId, string $sessionId, bool $force = false): array
    {
        $this->logger->logActivity('calcul_resultats_start', 'resultat', null, compact('concoursId', 'sessionId', 'force'));

        $this->validator->validateCalculPrerequis($concoursId, $sessionId, $force);
        $candidatures = $this->repository->getCandidaturesEligibles($concoursId, $sessionId);

        $stats = [
            'resultats_calcules' => [],
            'elimines' => [],
            'notes_manquantes' => [],
        ];

        return runTransaction(function () use ($candidatures, $concoursId, $sessionId, $force, &$stats) {
            foreach ($candidatures as $candidature) {
                try {
                    $resultat = $this->resultatProcessor->traiterResultat($candidature, $force);
                    $stats['resultats_calcules'][] = $resultat;
                } catch (\Exception $e) {
                    $this->categoriserErreur($e, $candidature, $stats);
                }
            }

            $this->logger->logActivity('calcul_resultats_success', 'resultat', null, [
                'concours_id' => $concoursId,
                'nombre_resultats' => count($stats['resultats_calcules']),
                'elimines' => count($stats['elimines']),
                'notes_manquantes' => count($stats['notes_manquantes'])
            ]);

            return $this->buildCalculResponse($concoursId, $sessionId, $candidatures->count(), $stats);
        }, 'ResultatService::calculerResultats');
    }

    /**
     * Déterminer les admissions pour une filière
     */
    public function determinerAdmissions(
        string $concoursId,
        string $sessionId,
        string $filiereId,
        bool $force = false,
        array $maxParRegion = []
    ): array {
        $this->logger->logActivity('determination_admissions_start', 'resultat', null, compact('concoursId', 'sessionId', 'filiereId', 'force'));

        $data = $this->validator->validateAdmissionPrerequis($concoursId, $sessionId, $filiereId, $force);
        // Les résultats sont ordonnés par mérite (moyenne desc) dans le repository.
        $resultats = $this->repository->getResultatsParFiliere($concoursId, $filiereId, $sessionId);

        // $maxParRegion est maintenant passé en argument
        // $maxParRegion = request()->input('max_par_region'); <-- SUPPRIMÉ
        // $maxParRegion = is_array($maxParRegion) ? $maxParRegion : []; <-- SUPPRIMÉ

        return runTransaction(function () use ($resultats, $data, $maxParRegion, $concoursId, $sessionId, $filiereId) {
            // Check if intelligent mode is enabled
            $rule = AdmissionRule::where('concours_id', $concoursId)
                ->where('session_id', $sessionId)
                ->where('est_actif', true)
                ->first();

            // Use intelligent mode if rule exists AND conditional admission enabled
            $useIntelligentMode = $rule && $rule->permet_admission_conditionnelle;

            // Handle percentage-based places
            $nombrePlaces = $data['nombre_places'];
            if ($rule && $rule->pourcentage_places_conditionnelles > 0 && $nombrePlaces <= 0) {
                // If total places is not set, we can calculate it as 10-15% of valid candidates
                $nombrePlaces = (int) ceil($resultats->count() * ($rule->pourcentage_places_conditionnelles / 100));
            }

            // Use stored quotas if not provided
            $actualQuotas = !empty($maxParRegion) ? $maxParRegion : ($rule->quotas_regionaux ?? []);

            if ($useIntelligentMode) {
                $stats = $this->intelligentAdmissionProcessor->process(
                    $resultats,
                    $nombrePlaces,
                    $actualQuotas,
                    $rule
                );
            } else {
                // Classic mode
                $stats = empty($actualQuotas)
                    ? $this->admissionProcessor->determiner($resultats, $nombrePlaces)
                    : $this->admissionProcessor->determinerAvecQuotasRegion($resultats, $nombrePlaces, $actualQuotas);
            }

            $this->logger->logActivity('determination_admissions_success', 'resultat', null, array_merge(compact('concoursId', 'filiereId'), $stats));

            return $this->buildAdmissionResponse(
                $concoursId,
                $data['session_id'],
                $filiereId,
                $data['filiere']->libelle_filiere,
                $data['nombre_places'],
                $resultats->count(),
                $stats,
                $actualQuotas
            );
        }, 'ResultatService::determinerAdmissions');
    }

    /**
     * Déterminer les admissions pour TOUTES les filières d'un concours/session
     */
    public function determinerToutesAdmissions(string $concoursId, string $sessionId, bool $force = false): array
    {
        $this->logger->logActivity('determination_all_admissions_start', 'resultat', null, compact('concoursId', 'sessionId', 'force'));

        $concoursFilieres = \App\Models\ConcoursFiliere::where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->with('filiere')
            ->get();

        if ($concoursFilieres->isEmpty()) {
            throw new ResultatException(
                ResultatException::FILIERE_INTROUVABLE,
                "Aucune filière configurée pour le concours {$concoursId} et la session {$sessionId}",
                "Aucune filière n'est configurée pour ce concours. Veuillez vérifier la configuration."
            );
        }

        $globalStats = [
            'filieres_traitees' => 0,
            'total_admis' => 0,
            'total_candidats' => 0,
            'details' => []
        ];

        foreach ($concoursFilieres as $cf) {
            try {
                $result = $this->determinerAdmissions($concoursId, $sessionId, $cf->filiere_id, $force, []);
                $globalStats['filieres_traitees']++;
                $globalStats['total_admis'] += $result['data']['admis'];
                $globalStats['total_candidats'] += $result['data']['nombre_candidats'];
                $globalStats['details'][] = $result['data'];
            } catch (\Exception $e) {
                $globalStats['details'][] = [
                    'filiere' => $cf->filiere->libelle_filiere,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'success' => true,
            'message' => 'Détermination globale des admissions terminée',
            'data' => $globalStats
        ];
    }

    /**
     * Traitement global : Calcul des résultats + Détermination de toutes les admissions
     */
    public function traiterResultatsGlobaux(string $concoursId, string $sessionId, bool $force = false): array
    {
        $this->logger->logActivity('traitement_global_start', 'resultat', null, compact('concoursId', 'sessionId', 'force'));

        // 1. Calculer les résultats pour tout le monde (Moyennes)
        $calculResult = $this->calculerResultats($concoursId, $sessionId, $force);

        // 2. Déterminer les admissions pour toutes les filières (Admissions)
        $admissionsResult = $this->determinerToutesAdmissions($concoursId, $sessionId, $force);

        $this->logger->logActivity('traitement_global_success', 'resultat', null, [
            'concours_id' => $concoursId,
            'session_id' => $sessionId,
            'filieres_traitees' => $admissionsResult['data']['filieres_traitees'],
        ]);

        return [
            'success' => true,
            'message' => 'Traitement global (Calcul + Admissions) terminé avec succès',
            'data' => [
                'calcul' => $calculResult['data'],
                'admissions' => $admissionsResult['data']
            ]
        ];
    }

    /**
     * Publier les résultats
     */
    public function publierResultats(
        string $concoursId,
        string $sessionId,
        ?string $datePrevue = null,
        ?string $message = null,
        bool $timerActif = false
    ): array {
        $this->logger->logActivity('publication_resultats_start', 'resultat', null, compact('concoursId', 'sessionId', 'timerActif'));

        $this->validator->validatePublicationPrerequis($concoursId, $sessionId);

        return runTransaction(function () use ($concoursId, $sessionId, $datePrevue, $message, $timerActif) {
            $publication = \App\Models\ResultatPublication::updateOrCreate(
                [
                    'concours_id' => $concoursId,
                    'session_id' => $sessionId,
                ],
                [
                    'date_publication_prevue' => $datePrevue ? Carbon::parse($datePrevue) : now(),
                    'date_publication_effective' => $timerActif ? null : now(),
                    'est_publie' => !$timerActif,
                    'message_candidat' => $message,
                    'timer_actif' => $timerActif,
                ]
            );

            // Lock concours after publication
            if (!$timerActif) {
                $concours = \App\Models\Concours::findOrFail($concoursId);
                $concours->update(['est_actif' => false]);
                
                // Notify all candidates about results publication
                $this->notifyCandidatesResultsPublished($concoursId, $sessionId, $message);
            }

            $this->logger->logActivity('publication_resultats_success', 'resultat', null, [
                'concours_id' => $concoursId,
                'session_id' => $sessionId,
                'timer_actif' => $timerActif,
                'publication_id' => $publication->id,
            ]);

            return [
                'success' => true,
                'message' => $timerActif ? 'Publication programmée avec succès' : 'Résultats publiés avec succès',
                'data' => $publication->toArray(),
            ];
        }, 'ResultatService::publierResultats');
    }

    /**
     * Notify all candidates that results have been published
     */
    private function notifyCandidatesResultsPublished(string $concoursId, string $sessionId, ?string $message): void
    {
        $candidatures = \App\Models\Candidature::where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->where('statut_candidature', \App\Enums\StatutCandidature::VALIDE->value)
            ->with('candidat.utilisateur')
            ->get();

        foreach ($candidatures as $candidature) {
            if ($candidature->candidat?->utilisateur) {
                // Create notification
                \App\Models\Notification::create([
                    'utilisateur_id' => $candidature->candidat->utilisateur->id,
                    'type_notification' => 'RESULTATS_PUBLIES',
                    'titre' => 'Résultats publiés',
                    'message' => $message ?? 'Les résultats de votre concours sont maintenant disponibles.',
                    'data' => json_encode([
                        'concours_id' => $concoursId,
                        'session_id' => $sessionId,
                        'candidature_id' => $candidature->id,
                    ]),
                    'est_lu' => false,
                ]);

                // TODO: Send email notification
                // event(new ResultatsPubliesEvent($candidature));
            }
        }

        $this->logger->logActivity('notifications_sent', 'resultat', null, [
            'concours_id' => $concoursId,
            'session_id' => $sessionId,
            'nombre_notifications' => $candidatures->count(),
        ]);
    }

    /**
     * Dépublier les résultats
     */
    public function depublierResultats(string $concoursId, string $sessionId): array
    {
        return runTransaction(function () use ($concoursId, $sessionId) {
            $publication = \App\Models\ResultatPublication::where('concours_id', $concoursId)
                ->where('session_id', $sessionId)
                ->firstOrFail();

            $publication->update([
                'est_publie' => false,
                'timer_actif' => false,
            ]);

            $this->logger->logActivity('depublication_resultats_success', 'resultat', null, [
                'concours_id' => $concoursId,
                'session_id' => $sessionId,
                'publication_id' => $publication->id,
            ]);

            return [
                'success' => true,
                'message' => 'Résultats dépubliés avec succès',
                'data' => $publication->toArray(),
            ];
        }, 'ResultatService::depublierResultats');
    }

    /**
     * Obtenir le résultat d'un candidat
     */
    public function getResultatCandidat(string $candidatureId)
    {
        return $this->repository->getResultatCandidat($candidatureId);
    }

    /**
     * Obtenir le classement pour une filière
     */
    public function getClassement(string $concoursId, string $sessionId, string $filiereId, int $perPage = 50)
    {
        return $this->repository->getClassement($concoursId, $sessionId, $filiereId, $perPage);
    }

    /**
     * Obtenir tous les résultats
     */
    public function getResultats(string $concoursId, string $sessionId, ?string $filiereId = null, int $perPage = 100)
    {
        return $this->repository->getResultats($concoursId, $sessionId, $filiereId, $perPage);
    }

    /**
     * Récupérer les résultats par ordre de mérite
     */
    public function getResultatsParOrdreDeMerite(string $concoursId, string $sessionId)
    {
        return $this->repository->getResultatsParOrdreDeMerite($concoursId, $sessionId);
    }

    /**
     * Catégoriser une erreur lors du calcul
     */
    private function categoriserErreur(\Exception $e, $candidature, array &$stats): void
    {
        $message = $e->getMessage();
        $data = ['candidature_id' => $candidature->id, 'raison' => $message];

        if (str_contains($message, 'éliminatoire')) {
            $stats['elimines'][] = $data;
        } elseif (str_contains($message, 'notes manquantes')) {
            $stats['notes_manquantes'][] = $data;
        } else {
            throw $e;
        }
    }

    /**
     * Construire la réponse du calcul
     */
    private function buildCalculResponse(
        string $concoursId,
        string $sessionId,
        int $nombreCandidatures,
        array $stats
    ): array {
        return [
            'success' => true,
            'message' => 'Résultats calculés avec succès',
            'data' => [
                'concours_id' => $concoursId,
                'session_id' => $sessionId,
                'nombre_candidatures' => $nombreCandidatures,
                'resultats_calcules' => count($stats['resultats_calcules']),
                'candidatures_eliminees' => count($stats['elimines']),
                'candidatures_notes_manquantes' => count($stats['notes_manquantes']),
                'date_calcul' => Carbon::now()->toDateTimeString(),
            ],
            'warnings' => [
                'elimines' => $stats['elimines'],
                'notes_manquantes' => $stats['notes_manquantes']
            ]
        ];
    }

    /**
     * Construire la réponse de détermination des admissions
     */
    private function buildAdmissionResponse(
        string $concoursId,
        string $sessionId,
        string $filiereId,
        string $filiereLibelle,
        int $nombrePlaces,
        int $nombreCandidats,
        array $stats,
        array $maxParRegion = []
    ): array {
        return [
            'success' => true,
            'message' => 'Admissions déterminées avec succès',
            'data' => [
                'concours_id' => $concoursId,
                'session_id' => $sessionId,
                'filiere_id' => $filiereId,
                'filiere' => $filiereLibelle,
                'nombre_places' => $nombrePlaces,
                'nombre_candidats' => $nombreCandidats,
                'max_par_region' => $maxParRegion,
                'admis' => $stats['admis'],
                'admis_standard' => $stats['admis_standard'] ?? 0,
                'admis_conditionnel' => $stats['admis_conditionnel'] ?? 0,
                'liste_attente' => $stats['liste_attente'],
                'non_admis' => $stats['non_admis'],
                'date_determination' => Carbon::now()->toDateTimeString(),
            ]
        ];
    }
}
