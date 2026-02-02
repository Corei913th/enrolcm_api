<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Registration\CheckEligibilityRequest;
use App\Http\Requests\Public\Registration\CompleteRegistrationRequest;
use App\Http\Requests\Public\Registration\UploadPaymentRequest;
use App\Services\Domain\Registration\RegistrationService;
use App\Services\Domain\Concours\Checkers\ConcoursReadinessChecker;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
  public function __construct(
    private readonly RegistrationService $registrationService,
    private readonly ConcoursReadinessChecker $readinessChecker
  ) {}

  /**
   * Récupère les informations du concours pour l'étape 1 (filières, sessions, etc.)
   * 
   * GET /api/v1/public/registration/concours/{concours}
   * 
   * @param string $concours UUID du concours
   * @return JsonResponse
   */
  public function getConcoursInfo(string $concours): JsonResponse
  {
    try {
      // 1. Charger le concours
      $concoursModel = \App\Models\Concours::findOrFail($concours);
      $readinessCheck = $this->readinessChecker->check($concoursModel);

      if (!$readinessCheck['ready']) {
        return api_error(
          'Ce concours n\'est pas disponible pour l\'inscription',
          ['raisons' => $readinessCheck['reasons']],
          403
        );
      }

      // 3. Charger les relations nécessaires
      $concoursModel->load([
        'ecole:id,libelle_ecole,code_ecole',
        'sessions:id,libelle_session',
        'filieres:id,libelle_filiere,code_filiere,departement_id',
        'filieres.departement:id,libelle_departement,code_departement',
        'specConcours:id,nom_spec'
      ]);

      // 4. Retourner les informations
      return api_success([
        'concours' => [
          'id' => $concoursModel->id,
          'libelle' => $concoursModel->libelle_concours,
          'description' => $concoursModel->description,
          'date_limite_depot' => $concoursModel->date_limite_depot?->format('Y-m-d'),
          'date_examen' => $concoursModel->date_examen?->format('Y-m-d'),
          'nbre_max_places' => $concoursModel->nbre_max_places,
          'frais_inscription' => $concoursModel->frais_inscription,
          'ecole' => [
            'id' => $concoursModel->ecole->id,
            'nom' => $concoursModel->ecole->libelle_ecole,
            'code' => $concoursModel->ecole->code_ecole,
          ],
          'spec_concours' => [
            'id' => $concoursModel->specConcours->id,
            'nom' => $concoursModel->specConcours->nom_spec,
          ],
        ],
        'sessions' => $concoursModel->sessions->map(fn($session) => [
          'id' => $session->id,
          'libelle' => $session->libelle_session,
        ]),
        'filieres' => $concoursModel->filieres->map(fn($filiere) => [
          'id' => $filiere->id,
          'nom' => $filiere->libelle_filiere,
          'code' => $filiere->code_filiere,
          'departement' => $filiere->departement ? [
            'id' => $filiere->departement->id,
            'nom' => $filiere->departement->libelle_departement,
          ] : null,
          'nombre_places' => $filiere->pivot->nombre_places ?? null,
        ]),
      ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      return api_error('Concours non trouvé', null, 404);
    } catch (\Exception $e) {
      return api_error('Erreur lors de la récupération des informations du concours', ['error' => $e->getMessage()], 500);
    }
  }

  /**
   * Vérifie l'éligibilité d'un candidat pour un concours
   * 
   * POST /api/v1/public/registration/check-eligibility
   * 
   * @param CheckEligibilityRequest $request
   * @return JsonResponse
   */
  public function checkEligibility(CheckEligibilityRequest $request): JsonResponse
  {
    try {
      $concours = \App\Models\Concours::findOrFail($request->concours_id);
      $this->readinessChecker->ensureReady($concours);

      $result = $this->registrationService->checkEligibility(
        $concours,
        $request->session_id,
        $request->only(['date_naissance', 'serie_bac', 'nationalite', 'filiere_id', 'annee_bac'])
      );

      if ($result['eligible']) {
        return api_success([
          'eligible' => true,
          'capacite' => $result['capacite'],
          'message' => 'Vous êtes éligible pour ce concours'
        ]);
      }

      return api_error(
        'Vous n\'êtes pas éligible pour ce concours',
        [
          'eligible' => false,
          'raisons_ineligibilite' => $result['raisons_ineligibilite'],
          'capacite' => $result['capacite']
        ],
        422
      );
    } catch (\Exception $e) {
      return api_error('Erreur lors de la vérification d\'éligibilité', ['error' => $e->getMessage()], 500);
    }
  }

  /**
   * Upload et validation automatique du paiement
   * 
   * POST /api/v1/public/registration/upload-payment
   * 
   * @param UploadPaymentRequest $request
   * @return JsonResponse
   */
  public function uploadPayment(UploadPaymentRequest $request): JsonResponse
  {
    try {
      $concours = \App\Models\Concours::findOrFail($request->concours_id);
      $this->readinessChecker->ensureReady($concours);

      // Upload du fichier
      $file = $request->file('preuve_paiement');
      $path = $file->store('paiements/preuves', 'public');

      $result = $this->registrationService->uploadPayment($concours, [
        'session_id' => $request->session_id,
        'reference_paiement' => $request->reference_paiement,
        'montant' => $request->montant,
        'date_paiement' => $request->date_paiement,
        'preuve_paiement_path' => $path,
        'eligibility_data' => $request->eligibility_data
      ]);

      return api_success([
        'upload_id' => $result['upload_id'],
        'statut' => $result['statut']->value,
        'auto_valide' => $result['auto_valide'],
        'ocr_success' => $result['ocr_success'],
        'ocr_data' => $result['ocr_data'],
        'validation_raisons' => $result['validation_raisons'] ?? [],
        'message' => $result['message']
      ]);
    } catch (\Exception $e) {
      return api_error('Erreur lors de l\'upload du paiement', ['error' => $e->getMessage()], 500);
    }
  }

  /**
   * Validation manuelle du paiement (si OCR échoue)
   * 
   * POST /api/v1/public/registration/validate-payment
   * 
   * @param Request $request
   * @return JsonResponse
   */
  public function validatePayment(\Illuminate\Http\Request $request): JsonResponse
  {
    try {
      $concours = \App\Models\Concours::findOrFail($request->concours_id);
      $this->readinessChecker->ensureReady($concours);

      $result = $this->registrationService->validatePayment($concours, [
        'upload_id' => $request->upload_id,
        'reference_paiement' => $request->reference,
        'montant' => $request->montant,
        'date_paiement' => $request->date_paiement,
        'banque' => $request->banque,
        'numero_compte' => $request->numero_compte ?? null,
      ]);

      return api_success([
        'statut' => $result['statut']->value,
        'token_temporaire' => $result['token_temporaire'],
        'validation_raisons' => $result['validation_raisons'] ?? [],
        'message' => $result['message']
      ]);
    } catch (\Exception $e) {
      return api_error('Erreur lors de la validation du paiement', ['error' => $e->getMessage()], 500);
    }
  }

  /**
   * Complète l'inscription en créant le compte et tous les enregistrements
   * 
   * POST /api/v1/public/registration/complete
   * 
   * @param CompleteRegistrationRequest $request
   * @return JsonResponse
   */
  public function complete(CompleteRegistrationRequest $request): JsonResponse
  {
    try {
      // Pour completeRegistration, on vérifie via le token si le concours est toujours valide
      $result = $this->registrationService->completeRegistration(
        $request->token_temporaire,
        $request->only(['email', 'telephone', 'password'])
      );

      return api_created([
        'user' => $result['user'],
        'candidat' => $result['candidat'],
        'candidature' => $result['candidature'],
        'auth_token' => $result['auth_token'],
        'message' => $result['message']
      ]);
    } catch (\DomainException $e) {
      return api_error($e->getMessage(), null, 400);
    } catch (\Exception $e) {
      return api_error('Erreur lors de la création du compte', ['error' => $e->getMessage()], 500);
    }
  }
}
