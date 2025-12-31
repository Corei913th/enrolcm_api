<?php

namespace App\Http\Controllers;

use App\DTOs\Ecoles\CreateEcoleDTO;
use App\Exceptions\Business\EcoleException;
use App\Http\Requests\Ecoles\StoreEcoleRequest;
use App\Http\Requests\Ecoles\UpdateEcoleRequest;
use App\Http\Resources\EcoleResource;
use App\Services\Ecoles\EcoleService;
use App\Services\Ecoles\EcoleFileService;
use App\Services\Ecoles\EcolePdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'École récupérée avec succès',
                new EcoleResource($ecole)
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
                'École récupérée avec succès',
                new EcoleResource($ecole)
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Créer une nouvelle école avec fichiers
     */
    public function store(StoreEcoleRequest $request): JsonResponse
    {
        try {
            $ecole = null;
            
            DB::transaction(function () use ($request, &$ecole) {
                // DEBUG: Voir ce qui est reçu
                \Log::info('Données reçues:', $request->except(['logo', 'embleme', 'header_frame']));
                
                // Créer l'école
                $ecoleData = CreateEcoleDTO::from($request->except(['logo', 'embleme', 'header_frame']));
                $ecole = $this->ecoleService->create($ecoleData);

                // Uploader les fichiers si présents
                $fileService = new EcoleFileService();
                
                if ($request->hasFile('logo')) {
                    $logoInfo = $fileService->uploadFile($ecole, $request->file('logo'), 'logo');
                    $ecole->update([
                        'logo_path' => $logoInfo['path'],
                        'logo_original_name' => $logoInfo['original_name'],
                    ]);
                }

                if ($request->hasFile('embleme')) {
                    $emblemeInfo = $fileService->uploadFile($ecole, $request->file('embleme'), 'embleme');
                    $ecole->update([
                        'embleme_path' => $emblemeInfo['path'],
                        'embleme_original_name' => $emblemeInfo['original_name'],
                    ]);
                }

                if ($request->hasFile('header_frame')) {
                    $headerInfo = $fileService->uploadFile($ecole, $request->file('header_frame'), 'header_frame');
                    $ecole->update([
                        'header_frame_path' => $headerInfo['path'],
                        'header_frame_original_name' => $headerInfo['original_name'],
                    ]);
                }
            });

            return api_created(
                new EcoleResource($ecole->fresh()),
                'École créée avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la création de l\'école: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une école avec fichiers
     */
    public function update(UpdateEcoleRequest $request, string $id): JsonResponse
    {
        try {
            $ecole = null;
            
            DB::transaction(function () use ($request, $id, &$ecole) {
                // Mettre à jour les données de base
                $ecoleData = CreateEcoleDTO::from($request->except(['logo', 'embleme', 'header_frame']));
                $ecole = $this->ecoleService->update($id, $ecoleData);

                // Uploader les nouveaux fichiers si présents
                $fileService = new EcoleFileService();
                
                if ($request->hasFile('logo')) {
                    $logoInfo = $fileService->uploadFile($ecole, $request->file('logo'), 'logo');
                    $ecole->update([
                        'logo_path' => $logoInfo['path'],
                        'logo_original_name' => $logoInfo['original_name'],
                    ]);
                }

                if ($request->hasFile('embleme')) {
                    $emblemeInfo = $fileService->uploadFile($ecole, $request->file('embleme'), 'embleme');
                    $ecole->update([
                        'embleme_path' => $emblemeInfo['path'],
                        'embleme_original_name' => $emblemeInfo['original_name'],
                    ]);
                }

                if ($request->hasFile('header_frame')) {
                    $headerInfo = $fileService->uploadFile($ecole, $request->file('header_frame'), 'header_frame');
                    $ecole->update([
                        'header_frame_path' => $headerInfo['path'],
                        'header_frame_original_name' => $headerInfo['original_name'],
                    ]);
                }
            });

            return api_updated(
                new EcoleResource($ecole->fresh()),
                'École mise à jour avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une école et ses fichiers
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $ecole = $this->ecoleService->getById($id);
                
                // Supprimer tous les fichiers
                $fileService = new EcoleFileService();
                $fileService->deleteAllFiles($ecole);
                
                // Supprimer l'école
                $this->ecoleService->delete($id);
            });
            
            return api_deleted('École et fichiers supprimés avec succès');
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
                'Écoles actives récupérées avec succès',
                EcoleResource::collection($ecoles)
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
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
            $fileService = new EcoleFileService();
            
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
                'Fichier uploadé avec succès',
                new EcoleResource($ecole->fresh())
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
            $fileService = new EcoleFileService();
            
            $fileService->deleteFile($ecole, $request->input('type'));

            // Mettre à jour l'école
            $pathField = $request->input('type') . '_path';
            $nameField = $request->input('type') . '_original_name';
            
            $ecole->update([
                $pathField => null,
                $nameField => null,
            ]);

            return api_success(
                'Fichier supprimé avec succès',
                new EcoleResource($ecole->fresh())
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
            $pdfService = new EcolePdfService();
            
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
            $pdfService = new EcolePdfService();
            
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
