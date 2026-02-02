<?php

namespace App\Http\Controllers\Admin\Documents;

use App\DTOs\Documents\ValidateDocumentDTO;
use App\Enums\StatutVerificationDocument;
use App\Http\Controllers\Controller;
use App\Http\Requests\Documents\ValidateDocumentRequest;
use App\Services\Domain\Candidature\DocumentService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentValidationController extends Controller
{
  public function __construct(
    private readonly DocumentService $documentService
  ) {}

  /**
   * Lister les documents en attente de validation
   * @return JsonResponse
   */
  public function enAttente(): JsonResponse
  {
    $perPage = request()->input('per_page', 100);
    $concoursId = request()->input('concours_id');

    $documents = $this->documentService->getAllForValidation($perPage, $concoursId);

    return api_paginated($documents, 'Documents en attente récupérés avec succès');
  }

  /**
   * Get document validation statistics
   * @return JsonResponse
   */
  public function stats(): JsonResponse
  {
    try {
      $concoursId = request()->input('concours_id');
      $stats = $this->documentService->getValidationStats($concoursId);

      return api_success($stats, 'Statistiques des documents');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Valider un document
   * @param string $documentId
   * @return JsonResponse
   */
  public function valider(string $documentId): JsonResponse
  {
    $document = $this->documentService->getDocumentById($documentId);
    $dto = ValidateDocumentDTO::fromRequest([
      'statut' => StatutVerificationDocument::VALIDE->value,
      'commentaire' => request()->input('commentaire')
    ]);
    $validatedDocument = $this->documentService->validateDocument($document, $dto, request()->user()->id);

    return api_success($validatedDocument, 'Document validé avec succès');
  }

  /**
   * Rejeter un document
   * @param string $documentId
   * @return JsonResponse
   */
  public function rejeter(string $documentId): JsonResponse
  {
    request()->validate([
      'motif_rejet' => 'required|string|max:1000'
    ]);

    $document = $this->documentService->getDocumentById($documentId);
    $dto = ValidateDocumentDTO::fromRequest([
      'statut' => StatutVerificationDocument::REJETE->value,
      'commentaire' => request()->input('motif_rejet')
    ]);
    $validatedDocument = $this->documentService->validateDocument($document, $dto, request()->user()->id);

    return api_success($validatedDocument, 'Document rejeté');
  }

  /**
   * Lister les documents en attente de validation (alias)
   * @return JsonResponse
   */
  public function index(): JsonResponse
  {
    return $this->enAttente();
  }

  /**
   * Valider ou rejeter un document
   * @param ValidateDocumentRequest $request
   * @param string $documentId
   * @return JsonResponse
   */
  public function validateDocument(ValidateDocumentRequest $request, string $documentId): JsonResponse
  {
    $document = $this->documentService->getDocumentById($documentId);
    $dto = ValidateDocumentDTO::fromRequest($request->validated());
    $validatedDocument = $this->documentService->validateDocument($document, $dto, $request->user()->id);

    $message = $dto->statut === StatutVerificationDocument::VALIDE->value
      ? 'Document validé avec succès'
      : 'Document rejeté';

    return api_success($validatedDocument, $message);
  }

  /**
   * Télécharger un document pour vérification
   * @param string $documentId 
   * @return BinaryFileResponse
   */
  public function downloadDocument(string $documentId): BinaryFileResponse
  {
    $document = $this->documentService->getDocumentById($documentId);

    $filePath = storage_path('app/public/' . str_replace('storage/', '', $document->fichier_url));

    if (! file_exists($filePath)) {
      abort(404, 'Fichier non trouvé');
    }

    return response()->download($filePath, $document->nom_original);
  }
}
