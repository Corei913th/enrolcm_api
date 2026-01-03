<?php

namespace App\Http\Controllers\Candidat\Documents;

use App\DTOs\Documents\SubmitDocumentDTO;
use App\Http\Controllers\Controller;
use App\Services\Documents\DocumentService;
use App\Http\Requests\Documents\SubmitDocumentRequest;
use App\Services\Candidature\CandidatureService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
  public function __construct(
    private readonly DocumentService $documentService,
    private readonly CandidatureService $candidatureService
  ) {}

  /**
   * Récupérer les documents requis pour un concours
   * @param string $concoursId
   * @return JsonResponse
   */
  public function documentsRequis(string $concoursId): JsonResponse
  {
    try {
      $documentsRequis = $this->documentService->getDocumentsRequisForConcours($concoursId);

      return api_success([
        'documents_requis' => $documentsRequis,
      ], 'Documents requis récupérés avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 400);
    }
  }

  /**
   * Soumettre un document pour une candidature
   * @param SubmitDocumentRequest $request
   * @return JsonResponse
   */
  public function submitDocument(SubmitDocumentRequest $request): JsonResponse
  {
    try {
      $dto = SubmitDocumentDTO::fromRequest($request->validated());
      $submittedDocument = $this->documentService->submitDocument($dto, $request->file('fichier'));

      return api_created($submittedDocument, 'Document soumis avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 400);
    }
  }

  /**
   * Récupérer le statut des documents pour une candidature
   * @param string $candidatureId
   * @return JsonResponse
   */
  public function documentStatus(string $candidatureId): JsonResponse
  {
    try {
      $candidature = $this->candidatureService->getCandidatureOrFail($candidatureId);
      $documentsStatus = $this->documentService->getRequiredDocumentsStatusForCandidature($candidature);

      return api_success([
        'candidature' => $candidature->only(['id', 'concours_id']),
        'documents_status' => $documentsStatus,
      ], 'Statut des documents récupéré avec succès');
    } catch (\DomainException $e) {
      return api_error($e->getMessage(), null, 404);
    }
  }

  /**
   * Télécharger un document soumis
   * @param string $documentId
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function downloadDocument(string $documentId): BinaryFileResponse
  {

    try {
      $document = $this->documentService->getDocumentById($documentId);

      if ($document->candidature->candidat_id !== request()->user()->candidat->id) {
        abort(403, 'Accès non autorisé');
      }

      $filePath = storage_path('app/public/' . str_replace('storage/', '', $document->fichier_url));

      if (!file_exists($filePath)) {
        abort(404, 'Fichier non trouvé');
      }

      return response()->download($filePath, $document->nom_original);
    } catch (\Exception $e) {
      abort(400, $e->getMessage());
    }
  }
}
