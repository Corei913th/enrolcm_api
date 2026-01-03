<?php

namespace App\Services\Documents;

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
use App\Enums\StatutVerificationDocument;
use DocumentStatutDTO;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use RuntimeException;


class DocumentService
{

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
    return DB::transaction(
      fn() =>
      DocumentRequis::create([
        'concours_id'      => $dto->concoursId,
        'nom_document'     => $dto->nomDocument,
        'description'      => $dto->description,
        'type_document'    => $dto->typeDocument->value,
        'est_obligatoire'  => $dto->estObligatoire,
        'format_accepte'   => $dto->formatAccepte,
        'taille_max_mb'    => $dto->tailleMaxMb,
        'ordre_affichage'  => $dto->ordreAffichage,
        'est_actif'        => true,
      ])
    );
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
    if ($documentRequis->documents()->exists()) {
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
    if ($documentRequis->documents()->exists()) {
      throw new DomainException(
        "Suppression interdite: documents déjà soumis"
      );
    }

    $documentRequis->delete();
  }


  /**
   * Soumet ou remplace un document pour une candidature donnée.
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
          'statut_verification'  => StatutVerificationDocument::EN_ATTENTE,
          'valide_par'           => null,
          'date_verification'    => null,
          'commentaire_verification' => null,
        ]
      );

      return $document;
    });
  }

  /**
   * Valide ou rejette un document soumis.
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
    int $userId
  ): Document {
    if ($document->statut_verification !== StatutVerificationDocument::EN_ATTENTE) {
      throw new DomainException("Document déjà traité");
    }

    return DB::transaction(function () use ($document, $dto, $userId) {

      match ($dto->statut) {
        StatutVerificationDocument::VALIDE =>
        $document->valider($userId, $dto->commentaire),

        StatutVerificationDocument::REJETE =>
        $document->rejeter(
          $userId,
          $dto->commentaire ?? 'Document rejeté'
        ),
      };

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
      ->whereDoesntHave('documents', function ($q) use ($candidature) {
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

    if (!in_array(
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
   * Get pending documents for validation.
   *
   * @param integer $perPage
   * @return LengthAwarePaginator
   */
  public function getDocumentsEnAttente(int $perPage = 20): LengthAwarePaginator
  {
    $relations = [
      'candidature.candidat:nom_cand,prenom_cand',
      'candidature.concours:libelle_concours',
      'documentRequis'
    ];
    return Document::with($relations)
      ->where('statut_verification', StatutVerificationDocument::EN_ATTENTE->value)
      ->orderBy('created_at', 'desc')
      ->paginate($perPage);
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
     * Récupère les documents requis pour une candidature avec le statut.
     *
     * @param Candidature $candidature
     * @return DocumentStatutDTO[]
     */
    public function getRequiredDocumentsStatusForCandidature(Candidature $candidature): array
    {
        
        $documentsRequis = DocumentRequis::where('concours_id', $candidature->concours_id)
            ->with(['documents' => function ($query) use ($candidature) {
                $query->where('candidature_id', $candidature->id);
            }])
            ->orderBy('ordre_affichage')
            ->get();

        return $documentsRequis->map(function ($documentRequis) {
            
            $document = $documentRequis->documents->first();

            return new DocumentStatutDTO(
                documentRequisId: $documentRequis->id,
                nom: $documentRequis->nom_document,
                estObligatoire: $documentRequis->est_obligatoire,
                statut: $document
                    ? StatutVerificationDocument::from($document->statut_verification)
                    : StatutVerificationDocument::NON_SOUMIS,
                commentaire: $document?->commentaire_verification
            );
        })->toArray();
    }
}
