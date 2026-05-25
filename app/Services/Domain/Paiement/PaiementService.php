<?php

namespace App\Services\Domain\Paiement;

use App\Enums\StatutPaiement;
use App\Enums\StatutVerificationPaiement;
use App\Models\Candidature;
use App\Models\ConcoursPaiement;
use App\Models\Paiement;
use App\Services\Domain\Candidature\Validators\CandidatureValidationService;
use App\Services\Domain\Notification\NotificationService;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Services\Infrastructure\OCR\PaymentOcrService;
use App\Traits\HasAdvancedSearch;
use App\Traits\HasSmartCache;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PaiementService
{
    use HasAdvancedSearch, HasSmartCache;

    public function __construct(
        private readonly PaymentOcrService $ocrService,
        private readonly ActivityLoggerService $logger,
        private readonly ConcoursPaiementService $concoursPaiementService,
        private readonly NotificationService $notificationService,
        private readonly CandidatureValidationService $candidatureValidationService
    ) {}

    /**
     * Créer un paiement avec OCR automatique
     */
    public function createPaymentWithOcr(string $concoursId, UploadedFile $preuve): array
    {
        return DB::transaction(function () use ($concoursId, $preuve) {
            // Sauvegarder la preuve
            $preuvePath = $preuve->store('paiements/preuves', 'public');

            // Extraire les données via OCR
            $ocrData = $this->ocrService->extractPaymentData($preuve);

            $concoursPaiement = ConcoursPaiement::where('concours_id', $concoursId)->first();
            $validationStatus = $ocrData['validation_status'] ?? StatutVerificationPaiement::PENDING_MANUAL_REVIEW;
            $validationNotes = $ocrData['validation_notes'] ?? null;
            $motifRejet = null;

            // VALIDATION STRICTE OCR
            if ($concoursPaiement) {
                $montantRequis = $concoursPaiement->montant;
                $montantExtrait = $ocrData['montant'] ?? 0;

                // Vérification Montant
                if (isset($ocrData['montant']) && $montantExtrait < $montantRequis) {
                    $validationStatus = StatutPaiement::REJECTED;
                    $motifRejet = "Montant insuffisant ({$montantExtrait} FCFA). Requis : {$montantRequis} FCFA";
                } elseif ($montantExtrait >= $montantRequis) {
                    $validationStatus = StatutPaiement::VERIFIED;
                    $validationNotes = "Validation automatique (Montant OK: {$montantExtrait})";
                } else {
                    // Montant non détecté par OCR -> Validation manuelle (ou auto-validé si on fait confiance à l'utilisateur)
                    // Pour l'instant, disons qu'on auto-valide si l'OCR échoue mais qu'on veut être permissif,
                    // MAIS l'utilisateur a demandé : "si un paiement correspond à celui demandé il est valide".
                    // Donc laissons une chance de validation manuelle si OCR échoue.
                    $validationStatus = StatutPaiement::PENDING_MANUAL_REVIEW;
                    $validationNotes = 'Montant non détecté par OCR. Vérification manuelle requise.';
                }
            } else {
                // Pas de config de paiement -> Auto-validation par défaut
                $validationStatus = StatutPaiement::VERIFIED;
                $validationNotes = 'Validation automatique (Pas de montant configuré)';
            }

            // Créer le paiement
            $paiement = Paiement::create([
                'concours_id' => $concoursId,
                'reference' => $ocrData['reference'] ?? null,
                'montant' => $ocrData['montant'] ?? null,
                'banque_ocr' => $ocrData['banque'] ?? null,
                'date_ocr' => $ocrData['date'] ?? null,
                'preuve_paiement' => $preuvePath,
                'statut' => $validationStatus,
                'validation_notes' => $validationNotes,
                'motif_rejet' => $motifRejet,
            ]);

            $this->logger->logActivity('create_payment_ocr', 'paiement', $paiement->id, [
                'concours_id' => $concoursId,
                'auto_validated' => $validationStatus === StatutPaiement::VERIFIED,
                'montant_requis' => $concoursPaiement->montant ?? 'N/A',
                'montant_ocr' => $ocrData['montant'] ?? 'N/A',
            ]);

            return [
                'paiement' => $paiement,
                'ocr_data' => $ocrData,
                'validation_info' => $ocrData['validation_info'] ?? [],
            ];
        });
    }

    /**
     * Créer un paiement manuel (avec données OCR)
     */
    public function createManualPayment(array $data): Paiement
    {
        $concoursPaiement = ConcoursPaiement::where('concours_id', $data['concours_id'])->first();

        // Trouver la candidature associée
        $candidature = Candidature::where('candidat_id', $data['candidat_id'] ?? null)
            ->where('concours_id', $data['concours_id'])
            ->latest()
            ->first();

        // Logique de validation par défaut
        $validationStatus = StatutPaiement::PENDING_MANUAL_REVIEW;
        $validationNotes = 'Validation manuelle requise';
        $motifRejet = null;

        // Si le statut est forcé dans les données
        if (isset($data['statut'])) {
            $validationStatus = $data['statut'];
        } else {
            // Sinon on applique la logique de validation automatique stricte
            if ($concoursPaiement) {
                $montantRequis = $concoursPaiement->montant;
                $montantSaisi = $data['montant'] ?? 0;

                if ($montantSaisi >= $montantRequis) {
                    $validationStatus = StatutPaiement::VERIFIED;
                    $validationNotes = "Validation automatique (Montant saisi OK: {$montantSaisi})";
                } elseif ($montantSaisi > 0) {
                    $validationStatus = StatutPaiement::REJECTED;
                    $motifRejet = "Montant insuffisant ({$montantSaisi} FCFA). Requis : {$montantRequis} FCFA";
                }
            } else {
                // Pas de config -> Auto-validation par défaut
                $validationStatus = StatutPaiement::VERIFIED;
                $validationNotes = 'Validation automatique (Pas de montant configuré)';
            }
        }

        $paiement = Paiement::create([
            'candidat_id' => $data['candidat_id'] ?? null,
            'concours_id' => $data['concours_id'],
            'candidature_id' => $candidature?->id, // Establish link
            'reference' => $data['reference'],
            'montant' => $data['montant'],
            'preuve_paiement' => $data['preuve_paiement'],
            'banque_ocr' => $data['banque_ocr'] ?? null,
            'date_ocr' => $data['date_ocr'] ?? null,
            'statut' => $validationStatus,
            'validation_notes' => $validationNotes,
            'motif_rejet' => $motifRejet,
        ]);

        // Si lié à un candidat, et candidature trouvée
        if ($candidature) {
            $candidature->update(['paiement_valide' => ($validationStatus === StatutPaiement::VERIFIED)]);

            try {
                if ($candidature->paiement_valide) {
                    $this->candidatureValidationService->checkAndValidateIfReady($candidature);
                }
            } catch (\Exception $e) {
                $this->logger->logActivity('candidature_auto_validation_failed_manual_payment', 'candidature', $candidature->id, [
                    'error' => $e->getMessage(),
                ]);
            }

            Cache::forget("dashboard_stats_{$data['candidat_id']}");
        }

        $this->logger->logActivity('create_manual_payment', 'paiement', $paiement->id);

        return $paiement;
    }

    /**
     * Vérifier si un PRU est valide
     */
    public function isPRUValid(string $pru, string $concoursId): array
    {
        $paiement = Paiement::where('reference', $pru)
            ->where('concours_id', $concoursId)
            ->first();

        if (! $paiement) {
            return [
                'valid' => false,
                'code' => 'PRU_NOT_FOUND',
                'message' => 'PRU introuvable pour ce concours',
            ];
        }

        if ($paiement->candidat_id) {
            return [
                'valid' => false,
                'code' => 'PRU_ALREADY_USED',
                'message' => 'Ce PRU est déjà utilisé',
            ];
        }

        if ($paiement->statut !== StatutVerificationPaiement::VERIFIED) {
            return [
                'valid' => false,
                'code' => 'PRU_NOT_VALIDATED',
                'message' => 'Ce PRU n\'est pas encore validé',
            ];
        }

        return [
            'valid' => true,
            'code' => 'PRU_VALID',
            'message' => 'PRU valide',
            'paiement' => $paiement,
        ];
    }

    /**
     * Récupérer les informations d'un paiement par PRU (paiement déjà effectué)
     */
    public function getPaiementInfo(string $pru): ?array
    {
        $paiement = Paiement::with('concours')->where('reference', $pru)->first();

        if (! $paiement) {
            return null;
        }

        return [
            'id' => $paiement->id,
            'concours_id' => $paiement->concours_id,
            'concours' => $paiement->concours,
            'montant' => $paiement->montant,
            'validated_at' => $paiement->validated_at,
            'statut' => $paiement->statut,
        ];
    }

    /**
     * Récupérer la configuration de paiement d'un concours (modalités, montant, etc.)
     */
    public function getConcoursPaymentConfig(string $concoursId): ?array
    {
        $config = $this->concoursPaiementService->getConfiguration($concoursId);

        if (! $config) {
            return null;
        }

        return [
            'montant' => $config->montant,
            'frais_paiement' => $config->frais_paiement,
            'montant_total' => $config->montantTotal(),
            'devise' => $config->devise,
            'date_limite' => $config->date_limite,
            'jours_restants' => $config->joursRestants(),
            'est_expire' => $config->isExpire(),
            'type_paiement' => $config->type_paiement,
            'banques_acceptees' => $config->banques_acceptees,
            'informations_bancaires' => $config->getInformationsBancaires(),
            'instructions' => $config->instructions,
            'validation_auto' => $config->validation_auto,
            'est_actif' => $config->est_actif,
        ];
    }

    /**
     * Lier un paiement à un candidat
     */
    public function linkToCandidat(string $pru, string $concoursId, string $candidatId): void
    {
        $paiement = Paiement::where('reference', $pru)
            ->where('concours_id', $concoursId)
            ->firstOrFail();

        $paiement->update(['candidat_id' => $candidatId]);

        $this->logger->logActivity('link_payment_to_candidat', 'paiement', $paiement->id, [
            'candidat_id' => $candidatId,
        ]);

        $this->invalidateCacheAfterModification($paiement->id);
    }

    /**
     * Récupérer les paiements avec filtres
     */
    public function getPayments(array $filters = [], int $perPage = 20)
    {
        $page = request()->input('page', 1);

        return $this->rememberList($filters, $page, $perPage, function () use ($filters, $perPage) {
            $query = Paiement::with(['candidat.utilisateur:id,user_name,email', 'concours']);

            // Filtres simples
            $simpleFilters = [];
            if (isset($filters['statut'])) {
                $simpleFilters['statut'] = $filters['statut'];
            }
            if (isset($filters['concours_id'])) {
                $simpleFilters['concours_id'] = $filters['concours_id'];
            }

            $query = $this->applyFilters($query, $simpleFilters);

            // Recherche
            if (isset($filters['search'])) {
                $query = $this->applySearch(
                    $query,
                    $filters['search'],
                    ['reference' => 'partial'],
                    [
                        'candidat.nom_cand' => 'partial',
                        'candidat.prenom_cand' => 'partial',
                    ]
                );
            }

            // Tri
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query = $this->applySort($query, $sortBy, $sortOrder, 'created_at', [
                'created_at',
                'montant',
                'statut',
            ]);

            return $query->paginate($perPage);
        }, 'paiements_list');
    }

    /**
     * Récupérer les paiements en attente
     */
    public function getPendingPayments(int $perPage = 20, ?string $concoursId = null)
    {
        $page = request()->input('page', 1);

        return $this->rememberList(['pending' => true, 'concours_id' => $concoursId], $page, $perPage, function () use ($perPage, $concoursId) {
            $query = Paiement::with([
                'candidature:id,candidat_id,concours_id,code_cand_def,numero_candidature',
                'candidature.candidat:utilisateur_id,nom_cand,prenom_cand',
                'candidature.candidat.utilisateur:id,email',
                'candidature.concours:id,libelle_concours',
            ])
                ->where('statut', StatutVerificationPaiement::PENDING_MANUAL_REVIEW);

            // Filter by concours if provided
            if ($concoursId) {
                $query->where('concours_id', $concoursId);
            }

            return $query->latest()->paginate($perPage);
        }, 'paiements_pending');
    }

    /**
     * Get all payments for validation (not just pending)
     */
    public function getAllForValidation(int $perPage = 100, ?string $concoursId = null)
    {
        $page = request()->input('page', 1);

        return $this->rememberList(['all_validation' => true, 'concours_id' => $concoursId], $page, $perPage, function () use ($perPage, $concoursId) {
            $query = Paiement::with([
                'candidature:id,candidat_id,concours_id,code_cand_def,numero_candidature',
                'candidature.candidat:utilisateur_id,nom_cand,prenom_cand',
                'candidature.candidat.utilisateur:id,email',
                'candidature.concours:id,libelle_concours',
            ]);

            // Filter by concours if provided
            if ($concoursId) {
                $query->where('concours_id', $concoursId);
            }

            return $query->latest()->paginate($perPage);
        }, 'paiements_all_validation');
    }

    /**
     * Get validation statistics
     */
    public function getValidationStats(?string $concoursId = null): array
    {
        $query = Paiement::query();

        if ($concoursId) {
            $query->where('concours_id', $concoursId);
        }

        $total = $query->count();
        $pending = (clone $query)->where('statut', StatutVerificationPaiement::PENDING)->count();
        $ocrVerifie = (clone $query)->where('statut', StatutVerificationPaiement::OCR_VERIFIE)->count();
        $manualReview = (clone $query)->where('statut', StatutVerificationPaiement::PENDING_MANUAL_REVIEW)->count();
        $verified = (clone $query)->where('statut', StatutPaiement::VERIFIED)->count();
        $rejected = (clone $query)->where('statut', StatutPaiement::REJECTED)->count();

        return [
            'total' => $total,
            'en_attente' => $pending + $ocrVerifie + $manualReview,
            'valides' => $verified,
            'rejetes' => $rejected,
        ];
    }

    /**
    }

    /**
     * Rejeter un paiement
     */
    public function reject(string $paiementId, string $motif, string $userId): Paiement
    {
        return DB::transaction(function () use ($paiementId, $motif, $userId) {
            $paiement = Paiement::with(['candidat.utilisateur', 'candidature'])->findOrFail($paiementId);

            // Update payment status (observer will handle notifications)
            $paiement->update([
                'statut' => StatutPaiement::REJECTED,
                'motif_rejet' => $motif,
                'validated_by' => $userId,
                'validated_at' => now(),
            ]);

            // Create critical alert
            if ($paiement->candidature) {
                $this->notificationService->createPaymentRejectedAlert($paiement->candidature, $motif);
            }

            $this->logger->logActivity('reject_payment', 'paiement', $paiementId, [
                'rejected_by' => $userId,
                'reason' => $motif,
            ]);

            $this->invalidateCacheAfterModification($paiementId);

            return $paiement->fresh(['candidat.utilisateur', 'candidature']);
        });
    }

    /**
     * Vérifier si une référence de paiement existe pour un concours
     */
    public function referenceExists(string $concoursId, string $reference): bool
    {
        return Paiement::where('concours_id', $concoursId)
            ->where('reference', $reference)
            ->exists();
    }

    /**
     * Créer un paiement
     */
    public function create(array $data): Paiement
    {
        $concoursPaiement = ConcoursPaiement::where('concours_id', $data['concours_id'])->first();

        // Logique de validation par défaut
        $validationStatus = $data['statut'] ?? StatutPaiement::PENDING_MANUAL_REVIEW;
        $validationNotes = 'Validation manuelle requise';
        $motifRejet = null;

        // Si le statut n'est pas déjà VERIFIED ou REJECTED (donc si on est en PENDING par défaut), on tente l'auto-validation
        // On permet au contrôleur de passer PENDING pour initier, mais on upgrade si possible.
        if ($validationStatus === StatutPaiement::PENDING || $validationStatus === StatutPaiement::PENDING_MANUAL_REVIEW) {
            if ($concoursPaiement) {
                $montantRequis = $concoursPaiement->montant;
                $montantSaisi = $data['montant'] ?? 0;

                if ($montantSaisi >= $montantRequis) {
                    $validationStatus = StatutPaiement::VERIFIED;
                    $validationNotes = "Validation automatique (Montant saisi OK: {$montantSaisi})";
                } elseif ($montantSaisi > 0) {
                    $validationStatus = StatutPaiement::REJECTED;
                    $motifRejet = "Montant insuffisant ({$montantSaisi} FCFA). Requis : {$montantRequis} FCFA";
                }
            } else {
                // Pas de config -> Auto-validation par défaut
                $validationStatus = StatutPaiement::VERIFIED;
                $validationNotes = 'Validation automatique (Pas de montant configuré)';
            }
        }

        $paiement = Paiement::create([
            'candidat_id' => $data['candidat_id'],
            'concours_id' => $data['concours_id'],
            'candidature_id' => $data['candidature_id'] ?? null,
            'reference' => $data['reference'],
            'montant' => $data['montant'],
            'banque_ocr' => $data['banque'] ?? null,
            'date_ocr' => $data['date_paiement'] ?? null,
            'preuve_paiement' => $data['preuve'] ?? null,
            'statut' => $validationStatus,
            'validation_notes' => $validationNotes,
            'motif_rejet' => $motifRejet,
        ]);

        // Mise à jour de la candidature (Linkage only)
        if (! empty($data['candidature_id'])) {
            // Just ensure logic knows about it?
            // Logic is handled by Observer observing 'created' payment status
            // We don't need to do anything here except cache clearing
            Cache::forget("dashboard_stats_{$data['candidat_id']}");
            $this->invalidateCacheAfterModification($paiement->id);
        }

        $this->logger->logActivity('create_payment', 'paiement', $paiement->id);

        return $paiement;
    }

    /**
     * Mettre à jour un paiement avec logique de validation
     */
    public function update(Paiement $paiement, array $data): Paiement
    {
        $concoursPaiement = ConcoursPaiement::where('concours_id', $paiement->concours_id)->first();

        // Logique de validation
        $validationStatus = $data['statut'] ?? StatutPaiement::PENDING_MANUAL_REVIEW;
        $validationNotes = 'Mise à jour du paiement - Validation manuelle requise';
        $motifRejet = null;
        $montant = $data['montant'] ?? $paiement->montant;

        // Si on repasse en PENDING, on tente l'auto-validation
        if ($validationStatus === StatutPaiement::PENDING || $validationStatus === StatutPaiement::PENDING_MANUAL_REVIEW) {
            if ($concoursPaiement) {
                $montantRequis = $concoursPaiement->montant;
                // On utilise le montant existant si pas de nouveau montant
                $montantATester = $montant;

                if ($montantATester >= $montantRequis) {
                    $validationStatus = StatutPaiement::VERIFIED;
                    $validationNotes = "Validation automatique suite mise à jour (Montant OK: {$montantATester})";
                } elseif ($montantATester > 0) {
                    $validationStatus = StatutPaiement::REJECTED;
                    $motifRejet = "Montant insuffisant ({$montantATester} FCFA). Requis : {$montantRequis} FCFA";
                }
            }
        }

        $updateData = [
            'statut' => $validationStatus,
            'motif_rejet' => $motifRejet,
            'validation_notes' => $validationNotes,
        ];

        if (isset($data['preuve_paiement'])) {
            $updateData['preuve_paiement'] = $data['preuve_paiement'];
        }
        if (isset($data['montant'])) {
            $updateData['montant'] = $data['montant'];
        }
        // Add other fields if necessary

        $paiement->update($updateData);

        // Check candidature linkage
        if (! $paiement->candidature_id && $paiement->candidat_id) {
            $candidature = Candidature::where('candidat_id', $paiement->candidat_id)
                ->where('concours_id', $paiement->concours_id)
                ->latest()
                ->first();
            if ($candidature) {
                $paiement->update(['candidature_id' => $candidature->id]);
            }
        }

        // Note: L'Observer se charge de mettre à jour la candidature et de notifier

        $this->invalidateCacheAfterModification($paiement->id); // Invalidate list caches
        Cache::forget("dashboard_stats_{$paiement->candidat_id}");

        $this->logger->logActivity('update_payment', 'paiement', $paiement->id);

        return $paiement;
    }

    /**
     * Retourne les tags de cache pour le modèle
     */
    protected function getModelTags(): array
    {
        return ['paiements', 'lists'];
    }
}
