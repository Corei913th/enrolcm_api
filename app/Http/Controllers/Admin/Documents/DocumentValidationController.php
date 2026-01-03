<?php

namespace App\Http\Controllers\Admin\Documents;

use App\DTOs\Documents\ValidateDocumentDTO;
use App\Enums\StatutVerificationDocument;
use App\Http\Controllers\Controller;
use App\Http\Requests\Documents\ValidateDocumentRequest;
use App\Services\Documents\DocumentService;
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
  public function index(): JsonResponse
  {
    $documents = $this->documentService->getDocumentsEnAttente();

    return api_success([
      'documents' => $documents,
    ], 'Documents en attente récupérés avec succès');
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
