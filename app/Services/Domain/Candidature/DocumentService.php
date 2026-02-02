<?php

namespace App\Services\Domain\Candidature;

use App\Models\{
  Document,
  DocumentRequis,
  Candidature
};
use App\DTOs\Documents\{
  CreateDocumentRequisDTO,
  UpdateDocumentRequisDTO,
  SubmitDocumentDTO,
  ValidateDocumentDTO
};
use App\Enums\{StatutVerificationDocument, StatutCandidature};
use App\Services\Domain\Candidature\Validators\CandidatureValidationService;
use App\Services\Domain\Notification\NotificationService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Traits\{HasSmartCache, HasAdvancedSearch};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use RuntimeException;


class DocumentService
{
  use HasSmartCache, HasAdvancedSearch;

  public function __construct(
    private readonly ActivityLoggerService $logger,
    private readonly CandidatureValidationService $candidatureValidationService,
    private readonly NotificationService $notificationService
  ) {}

  protected function getModelTags(): array
  {
    return ['documents', 'documents_requis'];
  }

  /**
   * Crée un document requis pour un concours donné.
   * @param CreateDocumentRequisDTO $dto Données validées du document requis
   *
   * @return DocumentRequis Le document requis nouvellement créé
   *
   * @throws \Throwable En cas d'échec de la transaction
   */
  public function createDocumentRequis(CreateDocumentRequisDTO $dto): DocumentRequis
  {
    return DB::transaction(function () use ($dto) {
      $documentRequis = DocumentRequis::create([
        'concours_id'      => $dto->concoursId,
        'nom_document'     => $dto->nomDocument,
        'description'      => $dto->description,
        'type_document'    => $dto->typeDocument->value,
        'est_obligatoire'  => $dto->estObligatoire,
        'format_accepte'   => $dto->formatAccepte,
        'taille_max_mb'    => $dto->tailleMaxMb,
        'ordre_affichage'  => $dto->ordreAffichage,
        'est_actif'        => true,
      ]);

      $this->logger->logActivity('create_document_requis', 'document_requis', $documentRequis->id, [
        'concours_id' => $dto->concoursId,
        'nom' => $dto->nomDocument
      ]);

      $this->flushModelCache();

      return $documentRequis;
    });
  }


  /**
   * Met à jour un document requis existant.
   * @param DocumentRequis $documentRequis Document requis à modifier
   * @param UpdateDocumentRequisDTO $dto Données de mise à jour validées
   *
   * @return DocumentRequis Le document requis mis à jour
   *
   * @throws \DomainException Si le document est déjà utilisé
   * @throws \Throwable En cas d'échec de transaction
   */
  public function updateDocumentRequis(DocumentRequis $documentRequis, UpdateDocumentRequisDTO $dto): DocumentRequis
  {
    if ($documentRequis->documentsSoumis()->exists()) {
      throw new DomainException(
        "Impossible de modifier un document requis déjà utilisé"
      );
    }

    $documentRequis->update([
      'nom_document'     => $dto->nomDocument,
      'description'      => $dto->description,
      'type_document'    => $dto->typeDocument->value,
      'est_obligatoire'  => $dto->estObligatoire,
      'format_accepte'   => $dto->formatAccepte,
      'taille_max_mb'    => $dto->tailleMaxMb,
      'ordre_affichage'  => $dto->ordreAffichage,
      'est_actif'        => $dto->estActif,
    ]);

    return $documentRequis->refresh();
  }

  /**
   * Supprime définitivement un document requis.
   * @param DocumentRequis $documentRequis Document requis à supprimer
   *
   * @return void
   *
   * @throws \DomainException Si des documents sont déjà associés
   */
  public function deleteDocumentRequis(DocumentRequis $documentRequis): void
  {
    if ($documentRequis->documentsSoumis()->exists()) {
      throw new DomainException(
        "Suppression interdite: documents déjà soumis"
      );
    }

    $documentRequis->delete();
  }


  /**
   * Soumet ou remplace un document pour une candidature donnée.
   * Les documents sont automatiquement validés par défaut.
   * 
   * @param SubmitDocumentDTO $dto Données de soumission validées
   *
   * @return Document Le document soumis ou mis à jour
   *
   * @throws \DomainException Si les règles métier sont violées
   * @throws \Throwable En cas d'échec de transaction
   */
  public function submitDocument(SubmitDocumentDTO $dto): Document
  {
    return DB::transaction(function () use ($dto) {

      $candidature = Candidature::lockForUpdate()->findOrFail($dto->candidatureId);
      $documentRequis = DocumentRequis::whereKey($dto->documentRequisId)
        ->where('est_actif', true)
        ->firstOrFail();

      $this->assertDocumentBelongsToConcours($candidature, $documentRequis);
      $this->assertFileIsValid($dto->fichier, $documentRequis);

      $newPath = $this->storeFile($dto->fichier);

      $document = Document::updateOrCreate(
        [
          'candidature_id'       => $candidature->id,
          'document_requis_id'   => $documentRequis->id,
        ],
        [
          'fichier_url'          => $newPath,
          'nom_original'         => $dto->fichier->getClientOriginalName(),
          'type_document'        => $dto->typeDocument->value,
          'statut_verification'  => StatutVerificationDocument::VALIDE, // ✅ Auto-validé
          'valide_par'           => null,
          'date_verification'    => now(),
          'commentaire_verification' => 'Validation automatique',
        ]
      );

      $this->logger->logActivity('submit_document', 'document', $document->id, [
        'candidature_id' => $candidature->id,
        'document_requis_id' => $documentRequis->id,
        'auto_validated' => true
      ]);

      // Vérifier si tous les documents sont complets
      if ($this->areDocumentsComplete($candidature)) {
        $candidature->update(['documents_complets' => true]);
      }

      // Tenter la validation automatique de la candidature
      try {
        $this->candidatureValidationService->checkAndValidateIfReady($candidature);
      } catch (\Exception $e) {
        $this->logger->logActivity('candidature_auto_validation_failed', 'candidature', $candidature->id, [
          'error' => $e->getMessage()
        ]);
        // Ne pas faire échouer la soumission du document
      }

      // Invalider le cache du dashboard
      Cache::forget("dashboard_stats_{$candidature->candidat_id}");

      $this->flushModelCache();

      return $document;
    });
  }

  /**
   * Valide ou rejette un document soumis.
   * Si un document est rejeté, la candidature est révoquée (retour à EN_COURS).
   * 
   * @param Document $document Document à valider ou rejeter
   * @param ValidateDocumentDTO $dto Données de validation
   * @param int $userId ID de l'utilisateur effectuant la validation
   *
   * @return Document Le document mis à jour
   *
   * @throws \DomainException Si le document n'est pas en attente
   * @throws \Throwable En cas d'échec de transaction
   */
  public function validateDocument(
    Document $document,
    ValidateDocumentDTO $dto,
    string $userId
  ): Document {
    if (
      $document->statut_verification !== StatutVerificationDocument::EN_ATTENTE &&
      $document->statut_verification !== StatutVerificationDocument::VALIDE
    ) {
      throw new DomainException("Document déjà traité");
    }

    return DB::transaction(function () use ($document, $dto, $userId) {

      $statut = is_string($dto->statut)
        ? StatutVerificationDocument::from($dto->statut)
        : $dto->statut;

      $candidature = $document->candidature;

      // Update document status (observer will handle notifications)
      match ($statut) {
        StatutVerificationDocument::VALIDE =>
        $document->valider($userId, $dto->commentaire),

        StatutVerificationDocument::REJETE =>
        $document->rejeter(
          $userId,
          $dto->commentaire ?? 'Document rejeté'
        ),
      };

      // Gérer la validation ou le rejet
      if ($statut === StatutVerificationDocument::VALIDE) {
        // Vérifier si tous les documents sont complets
        $allComplete = $this->areDocumentsComplete($candidature);

        if ($allComplete) {
          $candidature->update(['documents_complets' => true]);

          // Tenter la validation automatique
          try {
            $this->candidatureValidationService->checkAndValidateIfReady($candidature);
          } catch (\Exception $e) {
            $this->logger->logActivity('candidature_auto_validation_failed', 'candidature', $candidature->id, [
              'error' => $e->getMessage()
            ]);
          }
        }
      } elseif ($statut === StatutVerificationDocument::REJETE) {
        // ❌ REJET : Révoquer la validation de la candidature
        if ($candidature->statut_candidature === StatutCandidature::VALIDE) {
          $candidature->update([
            'statut_candidature' => StatutCandidature::SOUMISE,
            'documents_complets' => false,
          ]);

          $this->logger->logActivity('candidature_revoked_document_rejected', 'candidature', $candidature->id, [
            'document_id' => $document->id,
            'rejected_by' => $userId
          ]);

          // Invalider le cache
          Cache::forget("dashboard_stats_{$candidature->candidat_id}");
        }
      }

      return $document->refresh(['documentRequis', 'validePar']);
    });
  }




  /**
   * Vérifie si tous les documents obligatoires d'une candidature sont présents et validés.
   * @param Candidature $candidature Candidature à vérifier
   * @return bool True si tous les documents obligatoires sont validés
   */
  public function areDocumentsComplete(Candidature $candidature): bool
  {
    return DocumentRequis::where('concours_id', $candidature->concours_id)
      ->where('est_obligatoire', true)
      ->whereDoesntHave('documentsSoumis', function ($q) use ($candidature) {
        $q->where('candidature_id', $candidature->id)
          ->where('statut_verification', StatutVerificationDocument::VALIDE);
      })
      ->doesntExist();
  }



  /**
   * Vérifie qu'un document requis appartient bien au concours de la candidature.
   * @param Candidature $candidature Candidature concernée
   * @param DocumentRequis $documentRequis Document requis ciblé
   *
   * @return void
   *
   * @throws \DomainException Si les concours ne correspondent pas
   */
  private function assertDocumentBelongsToConcours(
    Candidature $candidature,
    DocumentRequis $documentRequis
  ): void {
    if ($candidature->concours_id !== $documentRequis->concours_id) {
      throw new DomainException(
        "Document requis non valide pour ce concours"
      );
    }
  }

  /**
   * Valide un fichier uploadé selon les contraintes du document requis.
   * @param UploadedFile $file Fichier soumis
   * @param DocumentRequis $documentRequis Contraintes applicables
   *
   * @return void
   *
   * @throws \DomainException Si le fichier est invalide
   */
  private function assertFileIsValid(
    UploadedFile $file,
    DocumentRequis $documentRequis
  ): void {
    if ($file->getSize() > ($documentRequis->taille_max_mb * 1024 * 1024)) {
      throw new DomainException("Fichier trop volumineux");
    }

    if ($documentRequis->format_accepte !== null && !in_array(
      $file->getClientOriginalExtension(),
      $documentRequis->format_accepte,
      true
    )) {
      throw new DomainException("Format de fichier non autorisé");
    }
  }

  /**
   * Stocke physiquement un fichier uploadé.
   * @param UploadedFile $file Fichier à stocker
   *
   * @return string Chemin public du fichier stocké
   *
   * @throws \RuntimeException En cas d'échec de stockage
   */
  private function storeFile(UploadedFile $file): string
  {
    return $file->storePubliclyAs(
      'documents',
      Str::uuid() . '.' . $file->guessExtension(),
      'public'
    );
  }


  /**
   * Récupère les documents requis pour un concours donné.
   *
   * @param string $concoursId ID du concours
   *
   * @return \Illuminate\Database\Eloquent\Collection Liste des documents requis
   */
  public function getDocumentsRequisForConcours(string $concoursId)
  {
    return DocumentRequis::where('concours_id', $concoursId)
      ->orderBy('ordre_affichage', 'asc')
      ->get();
  }

  /**
   * Get pending documents for validation (OPTIMISÉ avec cache et colonnes spécifiques).
   *
   * @param integer $perPage
   * @return LengthAwarePaginator
   */
  public function getDocumentsEnAttente(int $perPage = 20, ?string $concoursId = null): LengthAwarePaginator
  {
    $page = request()->input('page', 1);

    return $this->rememberList(
      ['statut' => 'en_attente', 'concours_id' => $concoursId],
      $page,
      $perPage,
      function () use ($perPage, $concoursId) {
        $query = Document::query()
          ->select([
            'documents.id',
            'documents.candidature_id',
            'documents.document_requis_id',
            'documents.fichier_url',
            'documents.nom_original',
            'documents.statut_verification',
            'documents.created_at'
          ])
          ->with([
            'candidature:id,candidat_id,concours_id,code_cand_def,numero_candidature',
            'candidature.candidat:utilisateur_id,nom_cand,prenom_cand',
            'candidature.concours:id,libelle_concours',
            'documentRequis:id,nom_document,type_document'
          ])
          ->where('documents.statut_verification', StatutVerificationDocument::EN_ATTENTE->value);

        // Filter by concours if provided
        if ($concoursId) {
          $query->whereHas('candidature', function ($q) use ($concoursId) {
            $q->where('concours_id', $concoursId);
          });
        }

        return $query->orderBy('documents.created_at', 'desc')
          ->paginate($perPage);
      },
      'documents_en_attente'
    );
  }

  /**
   * Get all documents for validation (not just pending)
   *
   * @param integer $perPage
   * @param string|null $concoursId
   * @return LengthAwarePaginator
   */
  public function getAllForValidation(int $perPage = 100, ?string $concoursId = null): LengthAwarePaginator
  {
    $page = request()->input('page', 1);

    return $this->rememberList(
      ['all_validation' => true, 'concours_id' => $concoursId],
      $page,
      $perPage,
      function () use ($perPage, $concoursId) {
        $query = Document::query()
          ->select([
            'documents.id',
            'documents.candidature_id',
            'documents.document_requis_id',
            'documents.fichier_url',
            'documents.nom_original',
            'documents.statut_verification',
            'documents.commentaire_verification',
            'documents.created_at'
          ])
          ->with([
            'candidature:id,candidat_id,concours_id,code_cand_def,numero_candidature',
            'candidature.candidat:utilisateur_id,nom_cand,prenom_cand',
            'candidature.concours:id,libelle_concours',
            'documentRequis:id,nom_document,type_document'
          ]);

        // Filter by concours if provided
        if ($concoursId) {
          $query->whereHas('candidature', function ($q) use ($concoursId) {
            $q->where('concours_id', $concoursId);
          });
        }

        return $query->orderBy('documents.created_at', 'desc')
          ->paginate($perPage);
      },
      'documents_all_validation'
    );
  }

  /**
   * Get validation statistics
   *
   * @param string|null $concoursId
   * @return array
   */
  public function getValidationStats(?string $concoursId = null): array
  {
    $query = Document::query();

    if ($concoursId) {
      $query->whereHas('candidature', function ($q) use ($concoursId) {
        $q->where('concours_id', $concoursId);
      });
    }

    $total = $query->count();
    $enAttente = (clone $query)->where('statut_verification', StatutVerificationDocument::EN_ATTENTE)->count();
    $valides = (clone $query)->where('statut_verification', StatutVerificationDocument::VALIDE)->count();
    $rejetes = (clone $query)->where('statut_verification', StatutVerificationDocument::REJETE)->count();

    return [
      'total' => $total,
      'en_attente' => $enAttente,
      'valides' => $valides,
      'rejetes' => $rejetes,
    ];
  }


  /**
   * Récupère un document par son ID avec les relations optionnelles.
   *
   * @param string $documentId ID du document
   * @param array $relations Relations à charger
   *
   * @return Document
   *
   * @throws \DomainException Si le document n'est pas trouvé
   */
  public function getDocumentById(string $documentId, array $relations = []): Document
  {
    try {
      return Document::with($relations)->findOrFail($documentId);
    } catch (ModelNotFoundException $e) {
      throw new DomainException("Document introuvable");
    }
  }

  /**
   * Récupère un document requis par son ID et le concours associé.
   *
   * @param string $documentId ID du document requis
   * @param string $concoursId ID du concours
   *
   * @return DocumentRequis
   *
   * @throws \DomainException Si le document n'est pas trouvé
   */
  public function getDocumentRequisByIdAndConcours(string $documentId, string $concoursId): DocumentRequis
  {
    try {
      return DocumentRequis::where('concours_id', $concoursId)
        ->where('id', $documentId)
        ->firstOrFail();
    } catch (ModelNotFoundException $e) {
      throw new DomainException("Document requis introuvable pour ce concours");
    }
  }

  /**
   * Get required documents status for candidature
   *
   * @param Candidature $candidature
   * @return array[]
   */
  public function getRequiredDocumentsStatusForCandidature(Candidature $candidature): array
  {

    $requiredDocuments = DocumentRequis::where('concours_id', $candidature->concours_id)
      ->with(['documentsSoumis' => function ($query) use ($candidature) {
        $query->where('candidature_id', $candidature->id);
      }])
      ->orderBy('ordre_affichage')
      ->get();

    return $requiredDocuments->map(function ($requiredDocument) {

      $document = $requiredDocument->documentsSoumis?->first();

      return [
        'document_requis_id' => $requiredDocument->id,
        'nom' => $requiredDocument->nom_document,
        'type_document' => $requiredDocument->type_document->value,
        'est_obligatoire' => $requiredDocument->est_obligatoire,
        'statut' => $document
          ? $document->statut_verification->value
          : StatutVerificationDocument::NON_SOUMIS->value,
        'commentaire' => $document?->commentaire_verification
      ];
    })->toArray();
  }

  /**
   * Get validated photo path for a candidature
   *
   * @param Candidature $candidature
   * @return string|null
   */
  public function getValidatedPhotoPath(Candidature $candidature): ?string
  {
    $photoDocument = $candidature->documents()
      ->whereHas('documentRequis', function ($q) {
        $q->where('type_document', \App\Enums\TypeDocument::PHOTO_IDENTITE);
      })
      ->where('statut_verification', \App\Enums\StatutVerificationDocument::VALIDE)
      ->first();

    if ($photoDocument && $photoDocument->fichier_url) {
      $fullPath = storage_path('app/' . $photoDocument->fichier_url);
      return file_exists($fullPath) ? $fullPath : null;
    }

    return null;
  }
}
