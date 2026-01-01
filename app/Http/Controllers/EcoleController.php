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


class EcoleController extends Controller
{
    public function __construct(
        private readonly EcoleService $ecoleService,
        private readonly EcoleFileService $fileService,
        private readonly EcolePdfService $pdfService
    ) {}

    /**
     * Get paginated list of schools with optional filters
     *
     * @param Request $request Available query params: est_actif, region, search, per_page
     * @return JsonResponse Paginated schools with EcoleResource
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['est_actif', 'region', 'search', 'per_page']);
            $ecoles = $this->ecoleService->getAll($filters);

            return api_paginated(
                $ecoles,
                'Liste des écoles récupérée avec succès',
                EcoleResource::class
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Get a specific school by ID with relationships loaded
     *
     * @param string $id School UUID
     * @return JsonResponse School data with EcoleResource
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
     * Get a school by its unique code
     *
     * @param string $code School code (unique identifier)
     * @return JsonResponse School data with EcoleResource
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
     * Create a new school with optional file uploads
     *
     * @param StoreEcoleRequest $request Validated request with school data and optional files
     * @return JsonResponse Created school with EcoleResource
     */
    public function store(StoreEcoleRequest $request): JsonResponse
    {
        try {
            $ecoleData = CreateEcoleDTO::from($request->except(['logo', 'embleme', 'header_frame']));
            $files = [];

            if ($request->hasFile('logo')) $files['logo'] = $request->file('logo');
            if ($request->hasFile('embleme')) $files['embleme'] = $request->file('embleme');
            if ($request->hasFile('header_frame')) $files['header_frame'] = $request->file('header_frame');

            $ecole = $this->ecoleService->createWithFiles($ecoleData, $files);

            return api_created(
                new EcoleResource($ecole),
                'École créée avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la création de l\'école: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing school with optional file uploads
     *
     * @param UpdateEcoleRequest $request Validated request with updated data and optional files
     * @param string $id School UUID to update
     * @return JsonResponse Updated school with EcoleResource
     */
    public function update(UpdateEcoleRequest $request, string $id): JsonResponse
    {
        try {

            $ecoleData = CreateEcoleDTO::from($request->except(['logo', 'embleme', 'header_frame']));
            $files = [];

            if ($request->hasFile('logo')) $files['logo'] = $request->file('logo');
            if ($request->hasFile('embleme')) $files['embleme'] = $request->file('embleme');
            if ($request->hasFile('header_frame')) $files['header_frame'] = $request->file('header_frame');


            $ecole = $this->ecoleService->updateWithFiles($id, $ecoleData, $files);

            return api_updated(
                new EcoleResource($ecole),
                'École mise à jour avec succès'
            );
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        } catch (\Exception $e) {
            return api_error('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Delete a school and all its associated files
     *
     * @param string $id School UUID to delete
     * @return JsonResponse Success message
     */
    public function destroy(string $id): JsonResponse
    {
        try {

            $this->ecoleService->deleteWithFiles($id);

            return api_deleted('École et fichiers supprimés avec succès');
        } catch (EcoleException $e) {
            return api_error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * Toggle the active status of a school
     *
     * @param string $id School UUID
     * @return JsonResponse Updated school with EcoleResource
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
     * Get list of all active schools for dropdown selections
     *
     * @return JsonResponse Collection of active schools with EcoleResource
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

    /**
     * Upload a file (logo, embleme, header_frame) for a school
     *
     * @param Request $request Must contain 'type' and 'file' fields
     * @param string $id School UUID
     * @return JsonResponse Updated school with EcoleResource
     */
    public function uploadFile(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:logo,embleme,header_frame',
            'file' => 'required|file|image|max:5120',
        ]);

        try {
            $ecole = $this->ecoleService->getById($id);

            $fileInfo = $this->fileService->uploadFile(
                $ecole,
                $request->file('file'),
                $request->input('type')
            );

            $ecole = $this->ecoleService->updateFileInfo(
                $ecole->id,
                $request->input('type'),
                $fileInfo
            );

            return api_success(
                new EcoleResource($ecole),
                'Fichier uploadé avec succès'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * Delete a file (logo, embleme, header_frame) for a school
     *
     * @param Request $request Must contain 'type' field
     * @param string $id School UUID
     * @return JsonResponse Updated school with EcoleResource
     */
    public function deleteFile(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:logo,embleme,header_frame',
        ]);

        try {
            $ecole = $this->ecoleService->getById($id);
            $this->fileService->deleteFile($ecole, $request->input('type'));


            $ecole = $this->ecoleService->clearFileInfo(
                $ecole->id,
                $request->input('type')
            );

            return api_success(
                new EcoleResource($ecole),
                'Fichier supprimé avec succès'
            );
        } catch (\Exception $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * Generate and download a PDF attestation document for a student
     *
     * @param Request $request Must contain student info fields
     * @param string $id School UUID
     * @return mixed PDF download response
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

            $pdf = $this->pdfService->generateAttestation($ecole, $request->all());

            return $pdf->download('attestation_' . $request->input('numero', 'ATT') . '.pdf');
        } catch (\Exception $e) {
            return api_error($e->getMessage());
        }
    }

    /**
     * Preview the PDF header for a school
     *
     * @param string $id School UUID
     * @return mixed PDF stream or download response
     */
    public function previewHeader(string $id)
    {
        try {
            $ecole = $this->ecoleService->getById($id);

            $pdf = $this->pdfService->generateDocument(
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
