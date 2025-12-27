<?php

namespace App\Http\Controllers\Candidats;

use App\Http\Controllers\Controller;
use App\DTOs\Candidats\UpdateCandidatDTO;
use App\Exceptions\Business\ResourceNotFoundException;
use App\Http\Requests\Candidats\UpdateCandidatRequest;
use App\Http\Resources\CandidatResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\Candidats\CandidatService;

class CandidatController extends Controller
{


    public function __construct(
        private readonly CandidatService $candidatService
    ) {}

    /**
     * Liste de tous les candidats avec pagination
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->only([
                'search',
                'region',
                'sexe_cand',
                'nationalite_cand',
                'include_inactive',
                'only_inactive'
            ]);

            $candidats = $this->candidatService->getAllCandidats($perPage, $filters);

            return api_paginated(
                CandidatResource::collection($candidats),
                'Liste des candidats récupérée avec succès'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Afficher un candidat spécifique
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $candidat = $this->candidatService->getCandidatById($id);

            return api_success([
                'candidat' => new CandidatResource($candidat),
            ]);
        } catch (ResourceNotFoundException $e) {
            return api_not_found($e->getMessage());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Mise à jour du candidat connecté (complétion du profil)
     *
     * @param UpdateCandidatRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCandidatRequest $request)
    {
        try {
            $dto = UpdateCandidatDTO::fromRequest($request);            
            $result = $this->candidatService->updateCandidat($dto);

            return api_updated([
                'candidat' => new CandidatResource($result),
            ], 'Profil mis à jour avec succès');
        } catch (ValidationException $e) {
            return api_validation_error($e->errors(), $e->getMessage());
        } catch (ResourceNotFoundException $e) {
            return api_not_found($e->getMessage());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Désactiver un candidat
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $this->candidatService->deleteCandidat($id);

            return api_deleted('Candidat désactivé avec succès');
        } catch (ResourceNotFoundException $e) {
            return api_not_found($e->getMessage());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Réactiver un candidat désactivé
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(string $id)
    {
        try {
            $this->candidatService->activateCandidat($id);

            return api_updated(null, 'Candidat réactivé avec succès');
        } catch (ResourceNotFoundException $e) {
            return api_not_found($e->getMessage());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Rechercher des candidats selon des critères
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $criteria = $request->only([
                'nom_cand',
                'prenom_cand',
                'numero_recu',
                'telephone_candidat',
                'region',
                'sexe_cand',
                'nationalite_cand',
            ]);

            $perPage = $request->input('per_page', 15);
            $candidats = $this->candidatService->searchCandidats($criteria, $perPage);

            return api_paginated(
                CandidatResource::collection($candidats),
                'Résultats de recherche'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Obtenir les statistiques des candidats
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            $stats = $this->candidatService->getCandidatStats();

            return api_success(['stats' => $stats]);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Récupérer un candidat par son numéro de reçu
     *
     * @param string $numero
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByNumeroRecu(string $numero)
    {
        try {
            $candidat = $this->candidatService->getCandidatByNumeroRecu($numero);

            return api_success([
                'candidat' => new CandidatResource($candidat),
            ]);
        } catch (ResourceNotFoundException $e) {
            return api_not_found($e->getMessage());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Vérifier la disponibilité d'un numéro de reçu
     *
     * @param string $numero
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNumeroRecu(string $numero)
    {
        try {
            $exists = $this->candidatService->numeroRecuExists($numero);

            return api_success([
                'exists' => $exists,
                'message' => $exists 
                    ? 'Ce numéro de reçu est déjà utilisé' 
                    : 'Numéro de reçu disponible',
            ]);
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }

    /**
     * Obtenir le profil du candidat connecté
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            $candidat = $this->candidatService->getCandidatById($user->id);

            return api_success([
                'candidat' => new CandidatResource($candidat),
            ]);
        } catch (ResourceNotFoundException $e) {
            return api_not_found($e->getMessage());
        } catch (\Exception $e) {
            return api_error($e->getMessage(), null, 500);
        }
    }
}
