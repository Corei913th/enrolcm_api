<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Documents\ValidateDocumentDTO;
use App\Enums\StatutVerificationDocument;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Services\Domain\Candidature\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller pour la validation des documents par les admins
 */
class DocumentValidationController extends Controller
{
  public function __construct(
    private readonly DocumentService $documentService
  ) {}

  /**
   * Liste des documents en attente de validation
   */
  public function enAttente(Request $request): JsonResponse
  {
    try {
      $perPage = $request->input('per_page', 20);

      $documents = $this->documentService->getDocumentsEnAttente($perPage);

      return api_paginated($documents, 'Documents en attente', DocumentResource::class);
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Valider un document
   */
  public function valider(Request $request, string $documentId): JsonResponse
  {
    $request->validate([
      'commentaire' => 'nullable|string|max:500'
    ]);

    try {
      $document = $this->documentService->getDocumentById($documentId, ['validePar', 'candidature']);

      $dto = new ValidateDocumentDTO(
        statut: StatutVerificationDocument::VALIDE->value,
        commentaire: $request->commentaire
      );

      $document = $this->documentService->validateDocument($document, $dto,  $request->user()->id);

      return api_success(new DocumentResource($document), 'Document validé avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Rejeter un document
   */
  public function rejeter(Request $request, string $documentId): JsonResponse
  {
    $request->validate([
      'commentaire' => 'required|string|max:500'
    ]);

    try {
      $document = $this->documentService->getDocumentById($documentId, ['validePar', 'candidature']);

      $dto = new ValidateDocumentDTO(
        statut: StatutVerificationDocument::REJETE->value,
        commentaire: $request->commentaire
      );

      $document = $this->documentService->validateDocument($document, $dto, $request->user()->id);

      return api_success(new DocumentResource($document), 'Document rejeté');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }
}
