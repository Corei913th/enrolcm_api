<?php

namespace App\Http\Controllers\Concours;

use App\Http\Controllers\Controller;
use App\Services\Domain\Concours\ConcoursService;
use App\Services\Domain\Concours\CentreService;
use App\Services\Domain\Concours\ConcoursFiliereService;
use App\Services\Domain\Paiement\ConcoursPaiementService;
use App\Services\Domain\Candidature\CandidatureService;
use App\Http\Requests\Concours\CreateConcoursRequest;
use App\Http\Requests\Concours\UpdateConcoursRequest;
use App\Http\Requests\Concours\ConfigurerPaiementRequest;
use App\Http\Requests\Concours\AttachCentreRequest;
use App\Http\Requests\Concours\UpdateCentreStatusRequest;
use App\Http\Requests\Concours\SyncCentresRequest;
use App\DTOs\Concours\CreateConcoursDTO;
use App\DTOs\Concours\UpdateConcoursDTO;
use App\Exceptions\ConcoursException;
use App\Http\Resources\ConcoursResource;
use App\Services\Infrastructure\Pdf\FicheInscriptionPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class ConcoursController extends Controller
{
    public function __construct(
        private readonly ConcoursService $concoursService,
        private readonly ConcoursPaiementService $paymentService,
        private readonly ConcoursFiliereService $filiereService,
        private readonly CentreService $centreService,
        private readonly CandidatureService $candidatureService,
        private readonly FicheInscriptionPdfService $ficheService
    ) {}

    /**
     * Liste des concours avec filtres et pagination (Caché 5 min).
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['statut', 'ecole_id', 'search']);
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        $cacheKey = 'concours_list_' . md5(json_encode($filters) . $perPage . $page);

        $concours = Cache::remember($cacheKey, 300, function () use ($filters, $perPage) {
            return $this->concoursService->getAll($filters, $perPage);
        });

        return api_paginated($concours, 'Liste des concours');
    }

    /**
     * Liste des concours disponibles (ouverts) (Caché 5 min).
     */
    public function availables(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);
        $cacheKey = "concours_availables_{$perPage}_{$page}";

        $concours = Cache::remember($cacheKey, 300, function () use ($perPage) {
            return $this->concoursService->getAvailableConcours($perPage);
        });

        return api_paginated($concours, 'Liste des concours ouverts');
    }

    /**
     * Détails d’un concours.
     */
    public function show(string $id): JsonResponse
    {
        $concours = Cache::remember("concours_detail_{$id}", 300, function () use ($id) {
            return $this->concoursService->getById($id, true);
        });
        return api_success(new ConcoursResource($concours));
    }

    /**
     * Créer un concours.
     */
    public function store(CreateConcoursRequest $request): JsonResponse
    {
        $dto = CreateConcoursDTO::fromRequest($request);
        $concours = $this->concoursService->create($dto);
        return api_created(new ConcoursResource($concours), 'Concours créé avec succès');
    }

    /**
     * Mettre à jour un concours.
     */
    public function update(string $id, UpdateConcoursRequest $request): JsonResponse
    {
        $dto = UpdateConcoursDTO::fromRequest($request);
        $concours = $this->concoursService->update($id, $dto);
        return api_updated(new ConcoursResource($concours), 'Concours mis à jour avec succès');
    }

    /**
     * Supprimer un concours.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->concoursService->delete($id);
        Cache::flush();
        return api_deleted('Concours supprimé avec succès');
    }

    /**
     * Activer un concours.
     */
    public function activate(string $id): JsonResponse
    {
        $concours = $this->concoursService->activate($id, true);
        Cache::flush();
        return api_success(new ConcoursResource($concours), 'Concours activé avec succès');
    }

    /**
     * Désactiver un concours.
     */
    public function deactivate(string $id): JsonResponse
    {
        $concours = $this->concoursService->deactivate($id, false);
        Cache::flush();
        return api_success(new ConcoursResource($concours), 'Concours désactivé avec succès');
    }

    /**
     * Assigner une spécification à un concours.
     */
    public function assignSpec(string $id, Request $request): JsonResponse
    {
        $request->validate([
            'spec_concours_id' => 'required|uuid|exists:specs_concours,id'
        ]);

        $concours = $this->concoursService->getById($id);
        $concours->spec_concours_id = $request->input('spec_concours_id');
        $concours->save();

        Cache::flush();
        return api_success(new ConcoursResource($concours), 'Spécification assignée avec succès');
    }

    /**
     * Configurer le paiement d’un concours.
     */
    public function configurePayment(string $id, ConfigurerPaiementRequest $request): JsonResponse
    {
        $config = $this->paymentService->configurerPaiement($id, $request->validated());
        return api_success($config, 'Configuration du paiement enregistrée');
    }

    /**
     * Obtenir les informations de paiement d’un concours.
     */
    public function paymentInfo(string $id): JsonResponse
    {
        $info = $this->paymentService->getConfiguration($id);
        return api_success($info);
    }

    /**
     * Obtenir les statistiques d’un concours.
     */
    public function stats(string $id): JsonResponse
    {
        $stats = $this->concoursService->getStats($id);
        return api_success($stats);
    }

    /**
     * Attacher une session à un concours.
     */
    public function attachSession(string $id, Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id');
        $this->concoursService->attachSession($id, $sessionId);
        return api_success(null, 'Session attachée au concours');
    }

    /**
     * Détacher une session d’un concours.
     */
    public function detachSession(string $id, string $sessionId): JsonResponse
    {
        $this->concoursService->detachSession($id, $sessionId);
        return api_success(null, 'Session détachée du concours');
    }

    /**
     * Changer l'état d'un concours dans une session.
     */
    public function changeSessionState(string $id, string $sessionId, Request $request): JsonResponse
    {
        $state = $request->input('etat');
        $this->concoursService->changeSessionState($id, $sessionId, $state);
        return api_success(null, 'État de la session mis à jour');
    }

    /**
     * Liste des centres.
     */
    public function listCentres(string $concours): JsonResponse
    {
        try {
            $centres = $this->centreService->listCentres($concours);
            return api_success($centres, 'Liste des centres');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Attacher un centre.
     */
    public function attachCentre(string $concours, AttachCentreRequest $request): JsonResponse
    {
        try {
            $centre = $this->centreService->attachCentre(
                $concours,
                $request->centre_id,
                $request->boolean('est_actif', true)
            );
            return api_success($centre, 'Centre attaché au concours');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Détacher un centre.
     */
    public function detachCentre(string $concours, string $centreId): JsonResponse
    {
        try {
            $this->centreService->detachCentre($concours, $centreId);
            return api_success(null, 'Centre détaché du concours');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Mettre à jour le statut d'un centre.
     */
    public function updateCentreStatus(string $concours, string $centreId, UpdateCentreStatusRequest $request): JsonResponse
    {
        try {
            $centre = $this->centreService->updateCentreStatus($concours, $centreId, $request->est_actif);
            return api_success($centre, 'Statut du centre mis à jour');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Synchroniser les centres.
     */
    public function syncCentres(string $concours, SyncCentresRequest $request): JsonResponse
    {
        try {
            $result = $this->centreService->syncCentres($concours, $request->centre_ids);
            return api_success($result, 'Centres synchronisés avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Liste des candidats pour un concours.
     */
    public function listCandidats(string $id, Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'statut_candidature', 'region', 'serie_bac', 'est_actif']);
        $perPage = $request->input('per_page', 20);

        $candidats = $this->candidatureService->getCandidatsForConcours($id, $filters, $perPage);

        return api_paginated($candidats, 'Liste des candidats du concours');
    }

    /**
     * Liste des filières attachées à un concours.
     */
    public function listFilieresAttachees(string $id): JsonResponse
    {
        $concours = $this->concoursService->getById($id);
        $session = $concours->sessions()->first();

        if (!$session) {
            return api_error('Ce concours n\'est attaché à aucune session', 400);
        }

        $filieres = $this->filiereService->listFilieres($id, $session->id);

        return api_success($filieres, 'Filières attachées au concours');
    }

    /**
     * Liste des filières disponibles (non attachées) pour un concours.
     */
    public function listFilieresDisponibles(string $id): JsonResponse
    {
        $filieres = $this->filiereService->getFilieresDisponibles($id);

        return api_success($filieres, 'Filières disponibles pour ce concours');
    }

    /**
     * Attacher une filière à un concours.
     */
    public function attachFiliere(string $id, Request $request): JsonResponse
    {
        $request->validate([
            'filiere_id' => 'required|exists:filieres,id',
            'nombre_places' => 'required|integer|min:1'
        ]);

        $concours = $this->concoursService->getById($id);
        $session = $concours->sessions()->first();

        if (!$session) {
            return api_error('Ce concours n\'est attaché à aucune session', 400);
        }

        $this->filiereService->attachFiliere(
            $id,
            $session->id,
            $request->input('filiere_id'),
            $request->input('nombre_places')
        );

        return api_success(null, 'Filière attachée avec succès');
    }

    /**
     * Détacher une filière d'un concours.
     */
    public function detachFiliere(string $id, string $filiereId): JsonResponse
    {
        $concours = $this->concoursService->getById($id);
        $session = $concours->sessions()->first();

        if (!$session) {
            return api_error('Ce concours n\'est attaché à aucune session', 400);
        }

        $this->filiereService->detachFiliere($id, $session->id, $filiereId);

        return api_success(null, 'Filière détachée avec succès');
    }

    /**
     * Mettre à jour le nombre de places d'une filière.
     */
    public function updateFiliereNombrePlaces(string $id, string $filiereId, Request $request): JsonResponse
    {
        $request->validate([
            'nombre_places' => 'required|integer|min:1'
        ]);

        $concours = $this->concoursService->getById($id);
        $session = $concours->sessions()->first();

        if (!$session) {
            return api_error('Ce concours n\'est attaché à aucune session', 400);
        }

        $this->filiereService->updateNombrePlaces(
            $id,
            $session->id,
            $filiereId,
            $request->input('nombre_places')
        );

        return api_success(null, 'Nombre de places mis à jour avec succès');
    }

    /**
     * Télécharger la fiche d'inscription PDF pour une candidature validée
     */
    public function telechargerFicheInscription(Request $request, string $candidatureId): mixed
    {
        try {
            $candidature = $this->candidatureService->getCandidatureOrFail($candidatureId);

            if (!$candidature->estValidee()) {
                return api_error('Seules les candidatures validées peuvent générer une fiche d\'inscription', 403);
            }
            $pdf = $this->ficheService->genererFicheInscription($candidature);

            $filename = 'fiche-inscription-' . ($candidature->numero_candidature ?? $candidature->code_cand_def) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return api_error('Erreur lors de la génération de la fiche: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Valider la cohérence des places (somme filières vs total concours).
     */
    public function validatePlaces(string $id, Request $request): JsonResponse
    {
        try {
            $sessionId = $request->input('session_id');
            $this->concoursService->validatePlacesCoherence($id, $sessionId);

            return api_success([
                'coherent' => true,
                'message' => 'La répartition des places est cohérente'
            ]);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), [
                'coherent' => false
            ], 422);
        }
    }

    /**
     * Obtenir un rapport détaillé sur la répartition des places.
     */
    public function placesReport(string $id, Request $request): JsonResponse
    {
        try {
            $sessionId = $request->input('session_id');
            $report = $this->concoursService->getPlacesReport($id, $sessionId);

            return api_success($report, 'Rapport de répartition des places');
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }
}
