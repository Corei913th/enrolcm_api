<?php

namespace App\Services\Domain\Paiement;

use App\Enums\StatutPaiement;
use App\Exceptions\Business\ManualPaymentValidationException;
use App\Models\ConcoursPaiement;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PaymentManualEntryService
{
  use HasActivityLogger;

  public function __construct(
    private readonly ConcoursPaiementService $concoursPaiementService,
    private readonly PaiementService $paiementService,
    ActivityLoggerService $logger
  ) {
    $this->logger = $logger;
  }

  /**
   * Crée un paiement avec données saisies manuellement
   * 
   * @param string $concoursId
   * @param string $reference
   * @param float $montant
   * @param string $banque
   * @param Carbon $datePaiement
   * @param UploadedFile $preuve
   * @return array ['paiement' => Paiement, 'validation_info' => array]
   * @throws ManualPaymentValidationException
   */
  public function createManualPayment(
    string $concoursId,
    string $reference,
    float $montant,
    string $banque,
    Carbon $datePaiement,
    UploadedFile $preuve
  ): array {
    return runTransaction(function () use ($concoursId, $reference, $montant, $banque, $datePaiement, $preuve) {
      $config = $this->concoursPaiementService->getConfiguration($concoursId);

      if (!$config || !$config->est_actif) {
        throw new ManualPaymentValidationException(
          ['concours' => ['Configuration de paiement non disponible pour ce concours']],
          'Configuration de paiement non disponible'
        );
      }

      $validationResult = $this->validateManualData([
        'reference' => $reference,
        'montant' => $montant,
        'banque' => $banque,
        'date_paiement' => $datePaiement,
        'concours_id' => $concoursId,
      ], $config);

      if (!$validationResult['valid']) {
        throw new ManualPaymentValidationException(
          $validationResult['errors'],
          'Données de paiement invalides'
        );
      }

      // Stocker le fichier de preuve
      $path = $preuve->store('paiements', 'public');

      // Créer le paiement avec statut PENDING_MANUAL_REVIEW
      $paiement = $this->paiementService->createManualPayment([
        'candidat_id' => null,
        'concours_id' => $concoursId,
        'reference' => $reference,
        'montant' => $montant,
        'preuve_paiement' => $path,
        'banque_ocr' => $banque,
        'date_ocr' => $datePaiement,
        'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
        'validation_notes' => 'Paiement créé avec saisie manuelle - En attente de validation administrative',
      ]);

      $this->logCreate('paiement_manuel', $paiement->id, ['reference' => $reference, 'concours_id' => $concoursId]);

      return [
        'paiement' => $paiement,
        'validation_info' => [
          'success' => true,
          'stored' => true,
          'code' => 'MANUAL_ENTRY_PENDING_REVIEW',
          'needs_manual_review' => true,
          'status' => StatutPaiement::PENDING_MANUAL_REVIEW->value,
          'message' => 'Paiement enregistré avec succès. Votre paiement sera validé par un administrateur dans les plus brefs délais.',
        ]
      ];
    }, 'PaymentManualEntryService::createManualPayment');
  }

  /**
   * Valide les données saisies manuellement
   * 
   * @param array $data
   * @param ConcoursPaiement $config
   * @return array ['valid' => bool, 'errors' => array]
   */
  public function validateManualData(array $data, ConcoursPaiement $config): array
  {
    $errors = [];

    // Vérifier que tous les champs obligatoires sont présents
    if (empty($data['reference'])) {
      $errors['reference'] = ['La référence de paiement est obligatoire'];
    }

    if (empty($data['montant'])) {
      $errors['montant'] = ['Le montant est obligatoire'];
    }

    if (empty($data['banque'])) {
      $errors['banque'] = ['Le nom de la banque est obligatoire'];
    }

    if (empty($data['date_paiement'])) {
      $errors['date_paiement'] = ['La date de paiement est obligatoire'];
    }

    // Si des champs obligatoires manquent, retourner immédiatement
    if (!empty($errors)) {
      return ['valid' => false, 'errors' => $errors];
    }

    // Vérifier l'unicité de la référence pour ce concours
    if ($this->paiementService->referenceExists($data['concours_id'], $data['reference'])) {
      $errors['reference'] = ['Cette référence de paiement est déjà utilisée pour ce concours'];
    }

    // Vérifier que le montant correspond à la configuration
    if ($data['montant'] != $config->montant) {
      $errors['montant'] = [
        "Le montant doit être de {$config->montant} FCFA (montant saisi: {$data['montant']} FCFA)"
      ];
    }

    // Vérifier que la date n'est pas dans le futur
    if ($data['date_paiement']->isFuture()) {
      $errors['date_paiement'] = ['La date de paiement ne peut pas être dans le futur'];
    }

    $concours = $config->concours;
    if ($concours && $concours->created_at) {
      $dateCreationConcours = Carbon::parse($concours->created_at);
      if ($data['date_paiement']->isBefore($dateCreationConcours->startOfDay())) {
        $errors['date_paiement'] = [
          "La date de paiement ne peut pas être antérieure à la date de création du concours ({$dateCreationConcours->format('d/m/Y')})"
        ];
      }
    }

    return [
      'valid' => empty($errors),
      'errors' => $errors
    ];
  }
}
