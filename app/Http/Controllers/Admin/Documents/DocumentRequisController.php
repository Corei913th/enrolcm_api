<?php

namespace App\Http\Controllers\Admin\Documents;

use App\DTOs\Documents\{CreateDocumentRequisDTO, UpdateDocumentRequisDTO};
use App\Http\Controllers\Controller;
use App\Models\DocumentRequis;
use App\Models\Concours;
use App\Services\Domain\Candidature\DocumentService;
use App\Http\Requests\Documents\CreateDocumentRequisRequest;
use App\Http\Requests\Documents\UpdateDocumentRequisRequest;
use Illuminate\Http\JsonResponse;

class DocumentRequisController extends Controller
{
  public function __construct(
    private readonly DocumentService $documentService
  ) {}

  /**
   * Lister les documents requis pour un concours
   * @param string $concoursId
   * @return JsonResponse
   */
  public function index(string $concoursId): JsonResponse
  {
    $concours = Concours::findOrFail($concoursId);
    $documents = $this->documentService->getDocumentsRequisForConcours($concoursId);

    return api_success([
      'concours' => $concours->only(['id', 'libelle_concours']),
      'documents_requis' => $documents,
    ], 'Documents requis récupérés avec succès');
  }

  /**
   * Créer un document requis
   * @param CreateDocumentRequisRequest $request
   */
  public function store(CreateDocumentRequisRequest $request): JsonResponse
  {
    try {
      $dto = CreateDocumentRequisDTO::fromRequest($request->validated());
      $document = $this->documentService->createDocumentRequis($dto);

      return api_created($document, 'Document requis créé avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 400);
    }
  }

  /**
   * Afficher un document requis
   * @param string $concoursId
   * @param string $documentId
   * @return JsonResponse
   */
  public function show(string $concoursId, string $documentId): JsonResponse
  {
    try {
      $document = DocumentRequis::where('concours_id', $concoursId)
        ->findOrFail($documentId);

      return api_success($document, 'Document requis récupéré avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 400);
    }
  }

  /**
   * Mettre à jour un document requis
   * @param UpdateDocumentRequisRequest $request
   * @param string $concoursId
   * @param string $documentId
   * @return JsonResponse
   */
  public function update(UpdateDocumentRequisRequest $request, string $concoursId, string $documentId): JsonResponse
  {
    try {
      $document = DocumentRequis::where('concours_id', $concoursId)
        ->findOrFail($documentId);

      $dto = UpdateDocumentRequisDTO::fromRequest($request->validated());
      $updatedDocument = $this->documentService->updateDocumentRequis($document, $dto);

      return api_success($updatedDocument, 'Document requis mis à jour avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 400);
    }
  }

  /**
   * Supprimer un document requis
   */
  public function destroy(string $concoursId, string $documentId): JsonResponse
  {
    try {
      $document = $this->documentService->getDocumentRequisByIdAndConcours($documentId, $concoursId);

      $this->documentService->deleteDocumentRequis($document);

      return api_success(null, 'Document requis supprimé avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 400);
    }
  }
}
