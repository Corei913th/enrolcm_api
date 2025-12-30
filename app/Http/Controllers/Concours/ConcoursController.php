<?php

namespace App\Http\Controllers\Concours;

use App\Http\Controllers\Controller;
use App\Services\Concours\ConcoursService;
use App\Services\Concours\ConcoursPaiementService;
use App\Http\Requests\Concours\CreateConcoursRequest;
use App\Http\Requests\Concours\UpdateConcoursRequest;
use App\Http\Requests\Concours\ConfigurerPaiementRequest;
use App\Http\Requests\Concours\AttachConcoursToSessionRequest;
use App\DTOs\Concours\CreateConcoursDTO;
use App\DTOs\Concours\UpdateConcoursDTO;
use App\DTOs\Concours\ConfigurePaymentDTO;
use App\DTOs\Concours\AttachConcoursToSessionDTO;
use App\Exceptions\ConcoursException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConcoursController extends Controller
{
    public function __construct(
        private readonly ConcoursService $concoursService,
        private readonly ConcoursPaiementService $paymentService
    ) {}

    /**
     * Liste des concours avec filtres et pagination.
     *
     * Endpoint : GET /api/concours
     *
     * @param Request $request Requête contenant filtres et pagination
     *
     * @return JsonResponse Réponse JSON paginée
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['est_actif', 'spec_concours_id', 'search', 'session_id']);
        $perPage = $request->input('per_page', 20);

        $concours = $this->concoursService->getAll($filters, $perPage);

        return api_paginated($concours, 'Liste des concours');
    }

    /**
     * Liste des concours disponibles (ouverts).
     *
     * Endpoint : GET /api/concours/availables
     *
     * @param Request $request Requête avec pagination
     *
     * @return JsonResponse Réponse JSON paginée
     */
    public function availables(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $concours = $this->concoursService->getAvailableConcours($perPage);

        return api_paginated($concours, 'Concours ouverts');
    }

    /**
     * Détails d’un concours.
     *
     * Endpoint : GET /api/concours/{id}
     *
     * @param string $id ID du concours
     *
     * @return JsonResponse Réponse JSON avec détails du concours
     */
    public function show(string $id): JsonResponse
    {
        try {
            $concours = $this->concoursService->getById($id);
            return api_success($concours);
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Créer un concours.
     *
     * Endpoint : POST /api/concours
     *
     * @param CreateConcoursRequest $request Requête validée
     *
     * @return JsonResponse Réponse JSON avec concours créé
     */
    public function store(CreateConcoursRequest $request): JsonResponse
    {
        try {
            $dto = CreateConcoursDTO::fromRequest($request->validated());
            $concours = $this->concoursService->create($dto);
            return api_created($concours, 'Concours créé avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Mettre à jour un concours.
     *
     * Endpoint : PUT /api/concours/{id}
     *
     * @param string $id ID du concours
     * @param UpdateConcoursRequest $request Requête validée
     *
     * @return JsonResponse Réponse JSON avec concours mis à jour
     */
    public function update(string $id, UpdateConcoursRequest $request): JsonResponse
    {
        try {
            $dto = UpdateConcoursDTO::fromRequest($request->validated());
            $concours = $this->concoursService->update($id, $dto);
            return api_success($concours, 'Concours mis à jour avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Supprimer un concours.
     *
     * Endpoint : DELETE /api/concours/{id}
     *
     * @param string $id ID du concours
     *
     * @return JsonResponse Réponse JSON succès ou erreur
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->concoursService->delete($id);
            return api_success(null, 'Concours supprimé avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Activer un concours.
     *
     * Endpoint : POST /api/concours/{id}/activate
     *
     * @param string $id ID du concours
     *
     * @return JsonResponse Réponse JSON avec concours activé
     */
    public function activate(string $id): JsonResponse
    {
        try {
            $concours = $this->concoursService->activate($id);
            return api_success($concours, 'Concours activé avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Désactiver un concours.
     *
     * Endpoint : POST /api/concours/{id}/deactivate
     *
     * @param string $id ID du concours
     *
     * @return JsonResponse Réponse JSON avec concours désactivé
     */
    public function deactivate(string $id): JsonResponse
    {
        try {
            $concours = $this->concoursService->deactivate($id);
            return api_success($concours, 'Concours désactivé avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Configurer le paiement d’un concours.
     *
     * Endpoint : POST /api/concours/{id}/configure-payment
     *
     * @param string $id ID du concours
     * @param ConfigurerPaiementRequest $request Requête validée
     *
     * @return JsonResponse Réponse JSON avec configuration enregistrée
     */
    public function configurePayment(string $id, ConfigurerPaiementRequest $request): JsonResponse
    {
        try {
            $dto = ConfigurePaymentDTO::fromRequest($request->validated());
            $config = $this->paymentService->configurePayment($id, $dto);
            return api_success($config, 'Configuration de paiement enregistrée avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Obtenir les informations de paiement d’un concours.
     *
     * Endpoint : GET /api/concours/{id}/payment-info
     *
     * @param string $id ID du concours
     *
     * @return JsonResponse Réponse JSON avec infos de paiement
     */
    public function paymentInfo(string $id): JsonResponse
    {
        try {
            $info = $this->paymentService->getPaymentInfo($id);
            return api_success($info);
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Obtenir les statistiques d’un concours.
     *
     * Endpoint : GET /api/concours/{id}/stats
     *
     * @param string $id ID du concours
     *
     * @return JsonResponse Réponse JSON avec statistiques
     */
    public function stats(string $id): JsonResponse
    {
        try {
            $stats = $this->concoursService->getStats($id);
            return api_success($stats);
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Attacher une session à un concours.
     *
     * Endpoint : POST /api/concours/{id}/attach-session
     *
     * @param string $id ID du concours
     * @param Request $request Requête contenant session_id
     *
     * @return JsonResponse Réponse JSON succès
     */
    public function attachSession(string $id, Request $request): JsonResponse
    {
        try {
            $request->validate(['session_id' => 'required|exists:sessions,id']);
            $this->concoursService->attachSession($id, $request->session_id);
            return api_success(null, 'Session attachée au concours avec succès (état: OUVERTE)');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Détacher une session d’un concours.
     *
     * Endpoint : DELETE /api/concours/{id}/detach-session/{sessionId}
     *
     * @param string $id ID du concours
     * @param string $sessionId ID de la session
     *
     * @return JsonResponse Réponse JSON succès ou erreur
     */
    public function detachSession(string $id, string $sessionId): JsonResponse
    {
        try {
            $this->concoursService->detachSession($id, $sessionId);
            return api_success(null, 'Session détachée du concours avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Modifier l’état d’une session liée à un concours.
     *
     * Endpoint : POST /api/concours/{id}/sessions/{sessionId}/change-state
     *
     * @param string $id ID du concours
     * @param string $sessionId ID de la session
     * @param Request $request Requête contenant le nouvel état (OUVERTE ou FERMEE)
     *
     * @return JsonResponse Réponse JSON succès ou erreur
     */
    public function changeSessionState(string $id, string $sessionId, Request $request): JsonResponse
    {
        try {
            $request->validate(['etat' => 'required|in:OUVERTE,FERMEE']);
            $this->concoursService->changeSessionState($id, $sessionId, $request->etat);
            return api_success(null, 'État de la session modifié avec succès');
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Obtenir l’état courant d’une session liée à un concours.
     *
     * Endpoint : GET /api/concours/{id}/sessions/{sessionId}/state
     *
     * @param string $id ID du concours
     * @param string $sessionId ID de la session
     *
     * @return JsonResponse Réponse JSON avec l’état courant
     */
    public function getSessionState(string $id, string $sessionId): JsonResponse
    {
        try {
            $etat = $this->concoursService->getCurrentSessionState($id, $sessionId);
            return api_success(['etat' => $etat]);
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Attacher un concours template à une session.
     *
     * Endpoint : POST /api/concours/{id}/attach-session
     *
     * @param AttachConcoursToSessionRequest $request Requête validée
     * @param string $id ID du concours template
     *
     * @return JsonResponse Réponse JSON avec le concours attaché
     */
    public function attachToSession(AttachConcoursToSessionRequest $request, string $id): JsonResponse
    {
        try {
            $dto = AttachConcoursToSessionDTO::fromRequest($request->validated());
            $concours = $this->concoursService->attachToSession($id, $dto->session_id, $dto->toArray());

            return api_success([
                'concours' => $concours->load('sessions'),
                'message' => 'Concours attaché à la session avec succès'
            ], 201);
        } catch (ConcoursException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Illuminate\Database\QueryException $e) {
            // Erreurs de base de données (contraintes, clés étrangères, etc.)
            return api_error('Erreur de base de données lors de l\'attachement du concours à la session', [
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        } catch (\Exception $e) {
            // Erreurs générales avec plus de contexte
            $errorContext = [
                'concours_id' => $id,
                'session_id' => $request->input('session_id'),
                'action' => 'attach_concours_to_session'
            ];

            return api_error(
                'Échec de l\'attachement du concours à la session: ' . $e->getMessage(),
                config('app.debug') ? $errorContext : null,
                500
            );
        }
    }
}
