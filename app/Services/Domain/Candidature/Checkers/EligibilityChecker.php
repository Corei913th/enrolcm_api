<?php

namespace App\Services\Domain\Candidature\Checkers;

use App\Enums\StatutCandidature;
use App\Enums\StatutPaiement;
use App\Helpers\CandidatureHelper;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\SpecConcours;
use App\Services\Domain\Candidature\DocumentService;
use Carbon\Carbon;

class EligibilityChecker
{
  public function __construct(
    private readonly DocumentService $documentService
  ) {}

  /**
   * Check complete eligibility of a candidature
   * @param Candidature $candidature
   * @return array ['eligible' => bool, 'reasons' => array]
   */
  public function checkFullEligibility(Candidature $candidature): array
  {
    $reasons = [];
    $eligible = true;

    // Check status
    // MEME SI VALIDE, on doit vérifier les champs et documents
    /*
    if ($candidature->statut_candidature === StatutCandidature::VALIDE) {
        return ['eligible' => true, 'reasons' => []];
    }
    */

    if ($candidature->statut_candidature !== StatutCandidature::VALIDE && $candidature->statut_candidature !== StatutCandidature::SOUMISE) {
      $eligible = false;
      $reasons[] = "La candidature doit avoir le statut SOUMISE ou VALIDE (statut actuel : {$candidature->statut_candidature->value})";
    }

    $fieldsCheck = CandidatureHelper::hasRequiredCandidateFields($candidature->id);
    if (!$fieldsCheck['valid']) {
      $eligible = false;
      $reasons[] = "Champs obligatoires manquants : " . implode(', ', $fieldsCheck['missing']);
    }

    if (!CandidatureHelper::hasValidPayment($candidature->id)) {
      $eligible = false;
      $reasons[] = "Paiement non vérifié";
    }

    // Check documents using optimized helper
    if (!CandidatureHelper::hasCompleteDocuments($candidature->id)) {
      $eligible = false;
      $reasons[] = "Documents incomplets ou non validés";
    }

    return [
      'eligible' => $eligible,
      'reasons' => $reasons
    ];
  }

  /**
   * Vérifie l'éligibilité avant l'inscription (sans Candidat existant)
   * Utilisé pour la validation publique avant création de compte
   * 
   * @param array $eligibilityData ['date_naissance' => string, 'serie_bac' => string, 'nationalite' => string]
   * @param SpecConcours $spec
   * @return array ['eligible' => bool, 'reasons' => array]
   */
  public function checkPreRegistrationEligibility(array $eligibilityData, SpecConcours $spec): array
  {
    $reasons = [];
    $eligible = true;

    // Vérifier l'âge (seulement si des restrictions sont définies)
    if ($spec->age_minimum !== null || $spec->age_maximum !== null) {
      if (isset($eligibilityData['date_naissance'])) {
        $age = Carbon::parse($eligibilityData['date_naissance'])->age;

        if ($spec->age_minimum !== null && $age < $spec->age_minimum) {
          $eligible = false;
          $reasons[] = "Âge minimum requis: {$spec->age_minimum} ans (âge actuel: {$age} ans)";
        }

        if ($spec->age_maximum !== null && $age > $spec->age_maximum) {
          $eligible = false;
          $reasons[] = "Âge maximum autorisé: {$spec->age_maximum} ans (âge actuel: {$age} ans)";
        }
      }
    }


    if (!empty($spec->series_bac_acceptees) && isset($eligibilityData['serie_bac'])) {
      if (!in_array($eligibilityData['serie_bac'], $spec->series_bac_acceptees, true)) {
        $eligible = false;
        $reasons[] = "Série de baccalauréat non acceptée. Séries acceptées: " . implode(', ', $spec->series_bac_acceptees);
      }
    }


    if (!empty($spec->nationalites_acceptees) && isset($eligibilityData['nationalite'])) {
      if (!in_array($eligibilityData['nationalite'], $spec->nationalites_acceptees, true)) {
        $eligible = false;
        $reasons[] = "Nationalité non acceptée. Nationalités acceptées: " . implode(', ', $spec->nationalites_acceptees);
      }
    }


    // Vérifier le diplôme requis
    if (!empty($spec->diplomes_requis) && isset($eligibilityData['diplome_admission'])) {
      if (!$spec->isDiplomeEligible($eligibilityData['diplome_admission'])) {
        $eligible = false;
        $reasons[] = "Diplôme non accepté. Diplômes requis: " . $spec->getDiplomesRequisFormatted();
      }
    }

    return [
      'eligible' => $eligible,
      'reasons' => $reasons
    ];
  }

  /**
   * Vérifie les critères académiques (âge, bac, nationalité)
   * 
   * @param Candidat $candidat
   * @param SpecConcours $spec
   * @return array ['eligible' => bool, 'reasons' => array]
   */
  public function checkAcademicEligibility(Candidat $candidat, SpecConcours $spec): array
  {
    $reasons = [];
    $eligible = true;

    // Vérifier l'âge (seulement si des restrictions sont définies)
    if ($spec->age_minimum !== null || $spec->age_maximum !== null) {
      $age = $candidat->date_naissance_cand
        ? Carbon::parse($candidat->date_naissance_cand)->age
        : $candidat->age_cand;

      if ($age !== null) {
        if ($spec->age_minimum !== null && $age < $spec->age_minimum) {
          $eligible = false;
          $reasons[] = "Âge minimum requis: {$spec->age_minimum} ans (âge actuel: {$age} ans)";
        }

        if ($spec->age_maximum !== null && $age > $spec->age_maximum) {
          $eligible = false;
          $reasons[] = "Âge maximum autorisé: {$spec->age_maximum} ans (âge actuel: {$age} ans)";
        }
      }
    }

    // Vérifier la série de baccalauréat (seulement si des restrictions sont définies)
    // Si series_bac_acceptees est null ou vide, toutes les séries sont acceptées
    if (!empty($spec->series_bac_acceptees) && $candidat->serie_bac !== null) {
      if (!in_array($candidat->serie_bac, $spec->series_bac_acceptees, true)) {
        $eligible = false;
        $reasons[] = "Série de baccalauréat non acceptée. Séries acceptées: " . implode(', ', $spec->series_bac_acceptees);
      }
    }

    // Vérifier la nationalité (seulement si des restrictions sont définies)
    // Si nationalites_acceptees est null ou vide, toutes les nationalités sont acceptées
    if (!empty($spec->nationalites_acceptees) && $candidat->nationalite_cand !== null) {
      if (!in_array($candidat->nationalite_cand, $spec->nationalites_acceptees, true)) {
        $eligible = false;
        $reasons[] = "Nationalité non acceptée. Nationalités acceptées: " . implode(', ', $spec->nationalites_acceptees);
      }
    }

    // Vérifier le diplôme requis (pour admission parallèle)
    if (!empty($spec->diplomes_requis) && $candidat->diplome_admission !== null) {
      if (!$spec->isDiplomeEligible($candidat->diplome_admission)) {
        $eligible = false;
        $diplomesText = $spec->getDiplomesRequisFormatted();
        $reasons[] = "Diplôme non accepté. Diplômes requis: {$diplomesText}";
      }
    }

    return [
      'eligible' => $eligible,
      'reasons' => $reasons
    ];
  }

  /**
   * Vérifie le statut du paiement
   * 
   * @param Candidature $candidature
   * @return array ['valid' => bool, 'status' => string, 'reason' => string]
   */
  public function checkPaymentStatus(Candidature $candidature): array
  {
    $paiement = $candidature->paiement;

    if (!$paiement) {
      return [
        'valid' => false,
        'status' => 'MISSING',
        'reason' => 'Aucun paiement associé à cette candidature'
      ];
    }

    if ($paiement->statut !== StatutPaiement::VERIFIED) {
      $statusLabel = match ($paiement->statut) {
        StatutPaiement::PENDING => 'en attente',
        StatutPaiement::PENDING_MANUAL_REVIEW => 'en attente de validation manuelle',
        StatutPaiement::REJECTED => 'rejeté',
        StatutPaiement::OCR_VERIFIE => 'vérifié par OCR mais non validé',
        default => 'non vérifié'
      };

      return [
        'valid' => false,
        'status' => $paiement->statut->value,
        'reason' => "Le paiement est {$statusLabel}"
      ];
    }

    return [
      'valid' => true,
      'status' => StatutPaiement::VERIFIED->value,
      'reason' => ''
    ];
  }

  /**
   * Vérifie le statut des documents
   * Utilise DocumentService.areDocumentsComplete() pour vérifier que tous les documents
   * obligatoires sont soumis et validés (statut VALIDE)
   * 
   * @param Candidature $candidature
   * @return array ['valid' => bool, 'missing' => array, 'pending' => array, 'rejected' => array]
   */
  public function checkDocumentsStatus(Candidature $candidature): array
  {
    $documentsComplete = $this->documentService->areDocumentsComplete($candidature);

    if ($documentsComplete) {
      return [
        'valid' => true,
        'missing' => [],
        'pending' => [],
        'rejected' => []
      ];
    }


    $documentsStatus = $this->documentService->getRequiredDocumentsStatusForCandidature($candidature);

    $missing = [];
    $pending = [];
    $rejected = [];

    foreach ($documentsStatus as $docStatus) {
      if ($docStatus['est_obligatoire']) {
        if ($docStatus['statut'] === \App\Enums\StatutVerificationDocument::NON_SOUMIS) {
          $missing[] = $docStatus['nom'];
        } elseif ($docStatus['statut'] === \App\Enums\StatutVerificationDocument::EN_ATTENTE) {
          $pending[] = $docStatus['nom'];
        } elseif ($docStatus['statut'] === \App\Enums\StatutVerificationDocument::REJETE) {
          $rejected[] = $docStatus['nom'];
        }
      }
    }

    return [
      'valid' => false,
      'missing' => $missing,
      'pending' => $pending,
      'rejected' => $rejected
    ];
  }

  /**
   * Vérifie si une candidature peut générer une convocation
   * @param Candidature $candidature
   * @return array ['eligible' => bool, 'reasons' => array]
   */
  public function canGenerateConvocation(Candidature $candidature): array
  {
    // Vérifier l'éligibilité de base (statut, paiement, documents)
    $baseEligibility = $this->checkFullEligibility($candidature);

    if (!$baseEligibility['eligible']) {
      return $baseEligibility;
    }

    $reasons = [];
    $eligible = true;

    // Vérifier que le centre d'examen est attribué
    if (!$candidature->centre_examen_id || !$candidature->centreExamen) {
      $eligible = false;
      $reasons[] = "Aucun centre d'examen n'a été attribué à cette candidature";
    }

    // Vérifier que le planning des épreuves est défini
    $concours = $candidature->concours;
    if ($concours) {
      $plannings = $concours->plannings()
        ->where('est_actif', true)
        ->count();

      if ($plannings === 0) {
        $eligible = false;
        $reasons[] = "Aucune épreuve n'a été planifiée pour ce concours";
      }
    } else {
      $eligible = false;
      $reasons[] = "Concours non trouvé";
    }

    return [
      'eligible' => $eligible,
      'reasons' => $reasons
    ];
  }

  /**
   * Vérifie si une candidature peut générer une fiche d'inscription
   * 
   * @param Candidature $candidature
   * @return array ['can_generate' => bool, 'reasons' => array]
   */
  public function canGenerateFicheInscription(Candidature $candidature): array
  {
    return $this->checkFullEligibility($candidature);
  }
}
