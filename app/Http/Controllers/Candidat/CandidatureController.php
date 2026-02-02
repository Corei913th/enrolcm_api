<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidats\CompleteCandidatureRequest;
use App\Http\Requests\Candidats\UploadPaymentReceiptRequest;
use App\Services\Domain\Candidature\CandidatureService;
use App\Services\Domain\Candidature\CandidatureCapabilitiesService;
use App\Services\Domain\Paiement\PaiementService;
use App\Services\Infrastructure\Pdf\FicheInscriptionPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidatureController extends Controller
{
  public function __construct(
    private readonly CandidatureService $candidatureService,
    private readonly CandidatureCapabilitiesService $capabilitiesService,
    private readonly PaiementService $paiementService,
    private readonly FicheInscriptionPdfService $ficheInscriptionPdfService
  ) {}

  /**
   * Liste des candidatures du candidat connecté.
   * 
   * @param Request $request Requête avec filtres optionnels (statut, concours_id)
   * @return JsonResponse Liste des candidatures avec relations
   */
  public function index(Request $request): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;

      $candidatures = $this->candidatureService->getCandidaturesByCandidat(
        $candidat->utilisateur_id,
        $request->only(['statut', 'concours_id'])
      );

      return api_success([
        'candidatures' => $candidatures,
        'total' => $candidatures->count()
      ], 'Liste des candidatures récupérée avec succès');
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Détails d'une candidature.
   * 
   * @param Request $request
   * @param string $id ID de la candidature
   * @return JsonResponse Détails complets avec toutes les relations
   */
  public function show(Request $request, string $id): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;
      $candidature = $this->candidatureService->getCandidatureOrFail($id);

      if ($candidature->candidat_id !== $candidat->utilisateur_id) {
        return api_error('Accès non autorisé à cette candidature', null, 403);
      }

      $candidature->load([
        'concours.ecole',
        'session',
        'paiement',
        'documents.documentRequis',
        'convocation',
        'resultatFinal',
        'centreExamen',
        'centreDepot'
      ]);

      return api_success($candidature, 'Détails de la candidature récupérés avec succès');
    } catch (\DomainException $e) {
      return api_error($e->getMessage(), null, 404);
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Résultat d'une candidature.
   * 
   * @param Request $request
   * @param string $id ID de la candidature
   * @return JsonResponse Résultat final avec décision d'admission
   */
  public function resultat(Request $request, string $id): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;
      $candidature = $this->candidatureService->getCandidatureOrFail($id);

      if ($candidature->candidat_id !== $candidat->utilisateur_id) {
        return api_error('Accès non autorisé à cette candidature', null, 403);
      }

      $resultat = $candidature->resultatFinal;

      if (!$resultat) {
        return api_error('Résultat non encore disponible', null, 404);
      }

      $candidature->load(['concours.ecole', 'session']);

      return api_success([
        'candidature' => $candidature->only(['id', 'code_cand_def', 'numero_candidature']),
        'concours' => $candidature->concours,
        'resultat' => $resultat
      ], 'Résultat récupéré avec succès');
    } catch (\DomainException $e) {
      return api_error($e->getMessage(), null, 404);
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Récupérer les centres disponibles pour une candidature.
   * 
   * @param Request $request
   * @param string $id ID de la candidature
   * @return JsonResponse Liste des centres disponibles
   */
  public function centres(Request $request, string $id): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;
      $candidature = $this->candidatureService->getCandidatureOrFail($id);

      if ($candidature->candidat_id !== $candidat->utilisateur_id) {
        return api_error('Accès non autorisé à cette candidature', null, 403);
      }

      $centres = $this->candidatureService->getCentresDisponibles($candidature->concours_id);

      return api_success($centres, 'Centres disponibles récupérés avec succès');
    } catch (\DomainException $e) {
      return api_error($e->getMessage(), null, 404);
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Compléter une candidature avec les centres.
   * 
   * @param CompleteCandidatureRequest $request
   * @param string $id ID de la candidature
   * @return JsonResponse Candidature mise à jour
   */
  public function complete(CompleteCandidatureRequest $request, string $id): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;
      $candidature = $this->candidatureService->getCandidatureOrFail($id);

      if ($candidature->candidat_id !== $candidat->utilisateur_id) {
        return api_error('Accès non autorisé à cette candidature', null, 403);
      }


      if ($candidature->concours->date_limite_depot && $candidature->concours->date_limite_depot->isPast()) {
        return api_error('La date limite de dépôt des dossiers est dépassée pour ce concours', null, 400);
      }

      $candidature = $this->candidatureService->completerCandidature($id, $request->validated());

      return api_success($candidature, 'Candidature complétée avec succès');
    } catch (\DomainException $e) {
      return api_error($e->getMessage(), null, 400);
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }


  /**
   * Télécharger la fiche d'inscription pour une candidature validée.
   * 
   * @param Request $request
   * @param string $id ID de la candidature
   * @return Response|JsonResponse PDF ou erreur
   */
  public function downloadInscriptionForm(Request $request, string $id)
  {
    try {
      $candidat = $request->user()->candidat;

      if (!$candidat) {
        return api_error('Candidat non trouvé', null, 404);
      }

      $candidature = $this->candidatureService->getCandidatureOrFail($id);

      if ($candidature->candidat_id !== $candidat->utilisateur_id) {
        return api_error('Accès non autorisé à cette candidature', null, 403);
      }

      if (!$candidature->estValidee()) {
        return api_error('La fiche d\'inscription n\'est disponible que pour les candidatures validées', null, 400);
      }

      $pdf = $this->ficheInscriptionPdfService->genererFicheInscription($candidature);
      $filename = 'fiche_inscription_' . ($candidature->numero_candidature ?? 'candidature') . '.pdf';

      return $pdf->download($filename);
    } catch (\DomainException $e) {
      \Log::error('DomainException in downloadInscriptionForm: ' . $e->getMessage());
      return api_error($e->getMessage(), null, 404);
    } catch (\Exception $e) {
      \Log::error('Exception in downloadInscriptionForm: ' . $e->getMessage(), [
        'trace' => $e->getTraceAsString()
      ]);
      return api_error('Erreur lors de la génération du PDF: ' . $e->getMessage(), null, 500);
    }
  }

  /**
   * Obtenir les capacités d'une candidature.
   * 
   * @param Request $request
   * @param string $id ID de la candidature
   * @return JsonResponse Capacités métier de la candidature
   */
  public function getCapabilities(Request $request, string $id): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;
      $candidature = $this->candidatureService->getCandidatureOrFail($id);

      if ($candidature->candidat_id !== $candidat->utilisateur_id) {
        return api_error('Accès non autorisé à cette candidature', null, 403);
      }

      $capabilities = $this->capabilitiesService->getCapabilities($candidature);

      return api_success($capabilities, 'Capacités de la candidature récupérées avec succès');
    } catch (\DomainException $e) {
      return api_error($e->getMessage(), null, 404);
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Récupérer les informations de paiement d'une candidature.
   * 
   * @param Request $request
   * @param string $id ID de la candidature
   * @return JsonResponse Informations de paiement
   */
  public function getPaiement(Request $request, string $id): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;
      $candidature = $this->candidatureService->getCandidatureOrFail($id);

      if ($candidature->candidat_id !== $candidat->utilisateur_id) {
        return api_error('Accès non autorisé à cette candidature', null, 403);
      }

      $candidature->load(['paiement', 'concours']);

      // Récupérer la configuration de paiement du concours
      $paymentConfig = $this->paiementService->getConcoursPaymentConfig($candidature->concours_id);

      return api_success([
        'paiement' => $candidature->paiement,
        'config' => $paymentConfig
      ], 'Informations de paiement récupérées avec succès');
    } catch (\DomainException $e) {
      return api_error($e->getMessage(), null, 404);
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Télécharger un reçu de paiement.
   * 
   * @param UploadPaymentReceiptRequest $request
   * @param string $id ID de la candidature
   * @return JsonResponse Paiement créé ou mis à jour
   */
  public function uploadPaymentReceipt(UploadPaymentReceiptRequest $request, string $id): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;
      $candidature = $this->candidatureService->getCandidatureOrFail($id);

      if ($candidature->candidat_id !== $candidat->utilisateur_id) {
        return api_error('Accès non autorisé à cette candidature', null, 403);
      }

      // Vérifier la date limite du concours
      if ($candidature->concours->date_limite_depot && $candidature->concours->date_limite_depot->isPast()) {
        return api_error('La date limite de dépôt des dossiers est dépassée pour ce concours', null, 400);
      }

      // Vérifier si un paiement existe déjà
      $paiement = $candidature->paiement;

      if ($paiement && $paiement->statut === \App\Enums\StatutPaiement::VERIFIED) {
        return api_error('Le paiement est déjà vérifié et ne peut plus être modifié', null, 400);
      }

      $validated = $request->validated();

      if ($paiement) {
        // Mettre à jour le paiement existant via le service
        $paiement = $this->paiementService->update($paiement, [
          'preuve_paiement' => $validated['preuve_paiement']->store('paiements/preuves', 'public'),
          'statut' => \App\Enums\StatutPaiement::PENDING
        ]);
      } else {
        // Créer un nouveau paiement via le service
        $paymentConfig = $this->paiementService->getConcoursPaymentConfig($candidature->concours_id);

        $paiement = $this->paiementService->create([
          'candidat_id' => $candidat->utilisateur_id,
          'candidature_id' => $candidature->id,
          'concours_id' => $candidature->concours_id,
          'reference' => 'PAY-' . strtoupper(uniqid()),
          'montant' => $paymentConfig['montant_total'] ?? 25000,
          'preuve' => $validated['preuve_paiement']->store('paiements/preuves', 'public'),
          'statut' => \App\Enums\StatutPaiement::PENDING
        ]);
      }

      return api_success($paiement->fresh(), 'Reçu de paiement téléchargé avec succès');
    } catch (\DomainException $e) {
      return api_error($e->getMessage(), null, 400);
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }

  /**
   * Supprimer un reçu de paiement.
   * 
   * @param Request $request
   * @param string $id ID de la candidature
   * @return JsonResponse Confirmation de suppression
   */
  public function deletePaymentReceipt(Request $request, string $id): JsonResponse
  {
    try {
      $candidat = $request->user()->candidat;
      $candidature = $this->candidatureService->getCandidatureOrFail($id);

      if ($candidature->candidat_id !== $candidat->utilisateur_id) {
        return api_error('Accès non autorisé à cette candidature', null, 403);
      }

      $paiement = $candidature->paiement;

      if (!$paiement) {
        return api_error('Aucun paiement trouvé', null, 404);
      }

      if ($paiement->statut === \App\Enums\StatutPaiement::VERIFIED) {
        return api_error('Le paiement vérifié ne peut pas être supprimé', null, 400);
      }

      // Supprimer le fichier de preuve
      if ($paiement->preuve_paiement) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($paiement->preuve_paiement);
      }

      // Supprimer le paiement
      $paiement->delete();

      return api_success(null, 'Reçu de paiement supprimé avec succès');
    } catch (\DomainException $e) {
      return api_error($e->getMessage(), null, 404);
    } catch (\Exception $e) {
      return api_error($e->getMessage(), null, 500);
    }
  }
}
