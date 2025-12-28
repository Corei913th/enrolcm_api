<?php

namespace App\Http\Controllers;

use App\DTOs\Ecoles\EcoleData;
use App\Exceptions\Business\EcoleException;
use App\Http\Requests\Ecoles\StoreEcoleRequest;
use App\Http\Requests\Ecoles\UpdateEcoleRequest;
use App\Http\Resources\EcoleResource;
use App\Services\Ecoles\EcoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EcoleController extends Controller
{
    protected EcoleService $ecoleService;

    public function __construct(EcoleService $ecoleService)
    {
        $this->ecoleService = $ecoleService;
    }

    /**
     * Liste des écoles avec filtres et pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['est_actif', 'region', 'search', 'per_page']);
            $ecoles = $this->ecoleService->getAll($filters);

            return api_paginated(
                EcoleResource::collection($ecoles)->resource,
                'Liste des écoles récupérée avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher une école spécifique par ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $ecole = $this->ecoleService->getById($id);
            
            return api_success(
                new EcoleResource($ecole),
                'École récupérée avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Afficher une école par son code
     */
    public function showByCode(string $code): JsonResponse
    {
        try {
            $ecole = $this->ecoleService->getByCode($code);
            
            return api_success(
                new EcoleResource($ecole),
                'École récupérée avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Créer une nouvelle école
     */
    public function store(StoreEcoleRequest $request): JsonResponse
    {
        try {
            $ecoleData = EcoleData::from($request->validated());
            $ecole = $this->ecoleService->create($ecoleData);

            return api_created(
                new EcoleResource($ecole),
                'École créée avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Mettre à jour une école
     */
    public function update(UpdateEcoleRequest $request, string $id): JsonResponse
    {
        try {
            $ecoleData = EcoleData::from($request->validated());
            $ecole = $this->ecoleService->update($id, $ecoleData);

            return api_updated(
                new EcoleResource($ecole),
                'École mise à jour avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Supprimer une école
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->ecoleService->delete($id);
            
            return api_deleted('École supprimée avec succès');
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Activer/Désactiver une école
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $ecole = $this->ecoleService->toggleStatus($id);
            
            return api_updated(
                new EcoleResource($ecole),
                'Statut de l\'école modifié avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Liste des écoles actives (pour les sélections)
     */
    public function active(): JsonResponse
    {
        try {
            $ecoles = $this->ecoleService->getActive();
            
            return api_success(
                EcoleResource::collection($ecoles),
                'Écoles actives récupérées avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }
}

    /**
     * Uploader un fichier pour une école
     */
    public function uploadFile(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:logo,embleme,header_frame',
            'file' => 'required|file|image|max:5120',
        ]);

        try {
            $ecole = $this->ecoleService->getById($id);
            $fileService = new \App\Services\Ecoles\EcoleFileService();
            
            $fileInfo = $fileService->uploadFile(
                $ecole,
                $request->file('file'),
                $request->input('type')
            );

            // Mettre à jour l'école avec les infos du fichier
            $pathField = $request->input('type') . '_path';
            $nameField = $request->input('type') . '_original_name';
            
            $ecole->update([
                $pathField => $fileInfo['path'],
                $nameField => $fileInfo['original_name'],
            ]);

            return api_success(
                new EcoleResource($ecole->fresh()),
                'Fichier uploadé avec succès'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * Supprimer un fichier d'une école
     */
    public function deleteFile(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:logo,embleme,header_frame',
        ]);

        try {
            $ecole = $this->ecoleService->getById($id);
            $fileService = new \App\Services\Ecoles\EcoleFileService();
            
            $fileService->deleteFile($ecole, $request->input('type'));

            // Mettre à jour l'école
            $pathField = $request->input('type') . '_path';
            $nameField = $request->input('type') . '_original_name';
            
            $ecole->update([
                $pathField => null,
                $nameField => null,
            ]);

            return api_success(
                new EcoleResource($ecole->fresh()),
                'Fichier supprimé avec succès'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * Générer une attestation PDF
     */
    public function generateAttestation(Request $request, string $id)
    {
        $request->validate([
            'etudiant_nom' => 'required|string',
            'numero' => 'nullable|string',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string',
        ]);

        try {
            $ecole = $this->ecoleService->getById($id);
            $pdfService = new \App\Services\Ecoles\EcolePdfService();
            
            $pdf = $pdfService->generateAttestation($ecole, $request->all());
            
            return $pdf->download('attestation_' . $request->input('numero', 'ATT') . '.pdf');
        } catch (\Exception $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * Prévisualiser l'entête PDF
     */
    public function previewHeader(string $id)
    {
        try {
            $ecole = $this->ecoleService->getById($id);
            $pdfService = new \App\Services\Ecoles\EcolePdfService();
            
            $pdf = $pdfService->generateDocument(
                $ecole,
                'APERÇU DE L\'ENTÊTE',
                '<p style="text-align: center; margin-top: 50px;">Ceci est un aperçu de l\'entête officielle de l\'école.</p>'
            );
            
            return $pdf->stream('preview_header.pdf');
        } catch (\Exception $e) {
            return api_error($e->getMessage());
        }
    }
}
