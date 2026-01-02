<?php

namespace App\Services\Payment;

use App\Models\Paiement;
use App\Enums\StatutPaiement;
use App\Services\OCR\TesseractOcrService;
use App\Services\Payment\ConcoursPaiementService;
use App\Services\Payment\Validators\PaymentOcrValidator;
use App\Services\Payment\Processors\OcrDataProcessor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaiementService
{
    public function __construct(
        private readonly TesseractOcrService $ocrService,
        private readonly ConcoursPaiementService $concoursPaiementService,
        private readonly OcrDataProcessor $processor
    ) {}

    /**
     * Crée un paiement avec preuve uploadée et données OCR.
     * @param string $concoursId UUID du concours
     * @param UploadedFile $preuve Fichier de preuve de paiement
     *
     * @return Paiement Paiement créé
     *
     * @throws \Exception Si la référence est déjà utilisée ou configuration inactive
     */
    /**
     * Crée un paiement avec OCR automatique (RECOMMANDÉ)
     * L'OCR extrait automatiquement référence et montant du reçu
     */
    public function createPaymentWithOcr(
        string $concoursId,
        UploadedFile $preuve
    ): array {
        return DB::transaction(function () use ($concoursId, $preuve) {
            $config = $this->concoursPaiementService->getConfiguration($concoursId);
            if (!$config || !$config->est_actif) {
                throw new \Exception('Configuration de paiement non disponible pour ce concours');
            }

            $path = $preuve->store('paiements', 'public');


            try {
                $fullPath = Storage::disk('public')->path($path);
                $ocrData = $this->ocrService->extractReceiptData($fullPath);
            } catch (\Exception $e) {
                Log::warning("OCR failed for payment: {$e->getMessage()}");
                return $this->processor->createFailedOcrPayment($concoursId, $path, $e);
            }


            $processor = new OcrDataProcessor();
            [$paiement, $errors, $warnings] = $processor->processOcrData($concoursId, $path, $ocrData, $config);

            if (!empty($errors)) {
                throw new \Exception(
                    'Le reçu n\'a pas pu être analysé correctement. ' .
                        'Informations essentielles manquantes: ' . implode(', ', $errors) .
                        '. Ces informations sont obligatoires pour valider le paiement.'
                );
            }

            // Tentative de validation automatique
            $validator = new PaymentOcrValidator();
            $validationResult = $validator->autoValidate($paiement, $config);


            $hasWarnings = !empty($warnings);
            $isAutoValidated = $validationResult;


            $validationCode = $this->getValidationCodeString($isAutoValidated, $hasWarnings);

            return [
                'paiement' => $paiement,
                'validation_info' => [
                    'success' => true,
                    'stored' => true,
                    'code' => $validationCode,
                    'complete_success' => $validationCode === 'VALIDATION_COMPLETE',
                    'needs_manual_review' => $validationCode === 'VALIDATION_MANUELLE_REQUISE',
                    'auto_validated' => $isAutoValidated,
                    'has_warnings' => $hasWarnings,
                    'warnings' => $warnings,
                    'status' => $paiement->statut->value,
                    'validation_notes' => $paiement->validation_notes,
                    'message' => $this->getValidationMessage($isAutoValidated, $hasWarnings, $warnings)
                ]
            ];
        });
    }

    /**
     * Retourne un code string pour le statut de validation.
     */
    private function getValidationCodeString(bool $isAutoValidated, bool $hasWarnings): string
    {
        if ($isAutoValidated && !$hasWarnings) {
            return 'VALIDATION_COMPLETE';
        }
        return 'VALIDATION_MANUELLE_REQUISE';
    }

    /**
     * Génère un message informatif selon le résultat de validation.
     */
    private function getValidationMessage(bool $isAutoValidated, bool $hasWarnings, array $warnings): string
    {
        if ($isAutoValidated && !$hasWarnings) {
            return 'Paiement valide et confirme automatiquement.';
        }
        return 'Paiement enregistre. Contactez l\'administration pour validation manuelle de votre recu.';
    }

    /**
     * Vérifie si un PRU (Paiement Référence Unique) est valide.
     *
     * @param string $pru Référence de paiement unique
     * @param string $concoursId ID du concours
     *
     * @return array Résultat de validation avec codes
     */
    public function isPRUValid(string $pru, string $concoursId): array
    {
        $paiement = Paiement::where('reference', $pru)
            ->where('concours_id', $concoursId)
            ->whereNull('candidat_id')
            ->first();

        if (!$paiement) {
            return [
                'valid' => false,
                'code' => 'PRU_INVALIDE',
                'message' => 'PRU invalide ou deja utilise',
                'concours' => null,
                'montant' => null,
                'paiement' => null
            ];
        }

        // Vérifier le statut du paiement
        if ($paiement->statut === StatutPaiement::REJECTED) {
            return [
                'valid' => false,
                'code' => 'PAIEMENT_REJETE',
                'message' => 'Paiement rejete par l\'administration',
                'concours' => $paiement->concours,
                'montant' => $paiement->montant,
                'paiement' => $paiement
            ];
        }

        if ($paiement->statut === StatutPaiement::PENDING_MANUAL_REVIEW) {
            return [
                'valid' => false,
                'code' => 'PAIEMENT_EN_ATTENTE_VALIDATION',
                'message' => 'Paiement en attente de validation par l\'administration',
                'concours' => $paiement->concours,
                'montant' => $paiement->montant,
                'paiement' => $paiement
            ];
        }

        if ($paiement->statut !== StatutPaiement::VERIFIED && $paiement->statut !== StatutPaiement::OCR_VERIFIE) {
            return [
                'valid' => false,
                'code' => 'PAIEMENT_NON_VALIDE',
                'message' => 'Paiement non valide',
                'concours' => $paiement->concours,
                'montant' => $paiement->montant,
                'paiement' => $paiement
            ];
        }

        $config = $paiement->concours->configurationPaiement;
        if ($config && $config->date_limite < now()) {
            return [
                'valid' => false,
                'code' => 'DATE_DEPASSEE',
                'message' => 'Date limite d\'inscription depassee',
                'concours' => $paiement->concours,
                'montant' => $paiement->montant,
                'paiement' => $paiement
            ];
        }

        return [
            'valid' => true,
            'code' => 'PRU_VALIDE',
            'message' => 'PRU valide',
            'concours' => $paiement->concours,
            'montant' => $paiement->montant,
            'paiement' => $paiement
        ];
    }

    /**
     * Liste des paiements avec filtres et pagination.
     *
     * @param array $filters Filtres à appliquer
     * @param int $perPage Nombre d'éléments par page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPayments(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {

        $query = Paiement::query()
            ->with([
                'candidat:id,utilisateur_id,nom_cand,prenom_cand',
                'candidat.utilisateur:id,email',
                'concours:id,libelle_concours'
            ])
            ->select([
                'id',
                'reference',
                'montant',
                'statut',
                'candidat_id',
                'concours_id',
                'created_at',
                'validated_at'
            ]);


        $query->when(
            !empty($filters['statut']),
            fn($q) =>
            $q->where('statut', $filters['statut'])
        )
            ->when(
                !empty($filters['concours_id']),
                fn($q) =>
                $q->where('concours_id', $filters['concours_id'])
            )
            ->when(
                !empty($filters['date_debut']),
                fn($q) =>
                $q->where('created_at', '>=', $filters['date_debut'])
            )
            ->when(
                !empty($filters['date_fin']),
                fn($q) =>
                $q->where('created_at', '<=', $filters['date_fin'])
            )
            ->when(!empty($filters['reference']), function ($q) use ($filters) {

                $q->where('reference', 'like', $filters['reference'] . '%');
            });


        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     *
     * @param string $query Terme de recherche
     * @param array $filters Filtres supplémentaires
     * @param int $perPage Nombre de résultats par page
     * @return LengthAwarePaginator Résultats de recherche
     */
    public function searchPayments(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        // Recherche full-text avec poids (A = très important, B = important, C = normal)
        $searchQuery = "
            search_vector @@ plainto_tsquery('french', ?) OR
            reference ILIKE ? OR
            validation_notes ILIKE ?
        ";

        $searchParams = [$query, "%{$query}%", "%{$query}%"];

        $results = Paiement::query()
            ->with([
                'candidat:id,utilisateur_id,nom_cand,prenom_cand',
                'candidat.utilisateur:id,email',
                'concours:id,libelle_concours'
            ])
            ->select([
                'id',
                'reference',
                'montant',
                'statut',
                'candidat_id',
                'concours_id',
                'created_at',
                'validated_at',
                'validation_notes'
            ])
            ->whereRaw($searchQuery, $searchParams)
            ->when(!empty($filters['statut']), fn($q) => $q->where('statut', $filters['statut']))
            ->when(!empty($filters['concours_id']), fn($q) => $q->where('concours_id', $filters['concours_id']))
            ->when(!empty($filters['date_debut']), fn($q) => $q->where('created_at', '>=', $filters['date_debut']))
            ->when(!empty($filters['date_fin']), fn($q) => $q->where('created_at', '<=', $filters['date_fin']))

            // Trier par pertinence (full-text ranking)
            ->orderByRaw("ts_rank(search_vector, plainto_tsquery('french', ?)) DESC", [$query])
            ->orderBy('created_at', 'desc')

            ->paginate($perPage);

        return $results;
    }

    /**
     * Statistiques des paiements (avec cache pour gros volumes).
     *
     * @param string|null $concoursId ID du concours (null = tous)
     * @return array Statistiques des paiements
     */
    public function getPaymentStats(?string $concoursId = null): array
    {
        $cacheKey = "payment_stats" . ($concoursId ? "_concours_{$concoursId}" : "_global");

        return Cache::remember($cacheKey, 1800, function () use ($concoursId) {
            $query = Paiement::query();

            if ($concoursId) {
                $query->where('concours_id', $concoursId);
            }

            return [
                'total' => $query->count(),
                'verified' => (clone $query)->where('statut', StatutPaiement::VERIFIED)->count(),
                'pending' => (clone $query)->where('statut', StatutPaiement::PENDING)->count(),
                'rejected' => (clone $query)->where('statut', StatutPaiement::REJECTED)->count(),
                'manual_review' => (clone $query)->where('statut', StatutPaiement::PENDING_MANUAL_REVIEW)->count(),
                'total_amount' => (clone $query)->where('statut', StatutPaiement::VERIFIED)->sum('montant'),
                'last_24h' => (clone $query)->where('created_at', '>=', now()->subDay())->count(),
            ];
        });
    }

    /**
     * Lie un paiement à un candidat.
     *
     * @param string $pru Référence de paiement unique
     * @param string $concoursId ID du concours
     * @param string $candidatId ID du candidat
     *
     * @return bool True si lié avec succès
     */
    public function linkToCandidat(string $pru, string $concoursId, string $candidatId): bool
    {
        $paiement = Paiement::where('reference', $pru)
            ->where('concours_id', $concoursId)
            ->where('statut', StatutPaiement::VERIFIED)
            ->whereNull('candidat_id')
            ->first();

        if ($paiement) {
            $paiement->update(['candidat_id' => $candidatId]);
            return true;
        }

        return false;
    }

    /**
     * Récupère les informations complètes d'un paiement depuis son PRU.
     *
     * @param string $pru Référence de paiement unique
     *
     * @return array|null Informations du paiement ou null
     */
    public function getPaiementInfo(string $pru): ?array
    {
        // OPTIMISATION AVANCÉE: Cache pour éviter les requêtes répétées
        $cacheKey = "paiement_info_{$pru}";

        return Cache::remember($cacheKey, 300, function () use ($pru) {
            $paiement = Paiement::with([
                'concours:id,libelle_concours,date_limite_depot,date_examen,est_actif',
                'concours.configurationPaiement:id,concours_id,banque_nom,numero_compte,montant,date_limite'
            ])
                ->where('reference', $pru)
                ->where('statut', StatutPaiement::VERIFIED)
                ->whereNull('candidat_id')
                ->select('id', 'concours_id', 'reference', 'montant', 'validated_at')
                ->first();

            if (!$paiement) {
                return null;
            }

            return [
                'paiement' => $paiement,
                'concours' => $paiement->concours,
                'concours_id' => $paiement->concours_id,
                'montant' => $paiement->montant,
                'validated_at' => $paiement->validated_at,
            ];
        });
    }

    /**
     * Récupère la date de validation d'un paiement.
     *
     * @param string $pru Référence de paiement unique
     * @param string $concoursId ID du concours
     *
     * @return \Carbon\Carbon|null Date de validation ou null
     */
    public function getValidationDate(string $pru, string $concoursId): ?\Carbon\Carbon
    {
        $paiement = Paiement::where('reference', $pru)
            ->where('concours_id', $concoursId)
            ->where('statut', StatutPaiement::VERIFIED)
            ->first();

        return $paiement?->validated_at;
    }

    /**
     * Liste des paiements en attente de validation.
     *
     * @param int $perPage Nombre d'éléments par page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPendingPayments(int $perPage = 20): LengthAwarePaginator
    {
        return Paiement::with(['candidat', 'concours'])
            ->whereIn('statut', [
                StatutPaiement::PENDING,
                StatutPaiement::PENDING_MANUAL_REVIEW
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Rejet manuel d'un paiement par un administrateur.
     *
     * @param int $paiementId ID du paiement
     * @param string $motif Motif du rejet
     * @param int $userId ID de l'utilisateur qui rejette
     *
     * @return Paiement
     *
     * @throws \Exception Si le paiement n'existe pas ou ne peut pas être rejeté
     */
    public function reject(int $paiementId, string $motif, int $userId): Paiement
    {
        $paiement = Paiement::findOrFail($paiementId);


        if (!in_array($paiement->statut, [
            StatutPaiement::PENDING,
            StatutPaiement::PENDING_MANUAL_REVIEW
        ])) {
            throw new \Exception('Ce paiement ne peut pas être rejeté');
        }


        $paiement->update([
            'statut' => StatutPaiement::REJECTED,
            'validated_at' => now(),
            'validated_by' => $userId,
            'validation_notes' => ($paiement->validation_notes ? $paiement->validation_notes . '; ' : '') . 'Rejeté: ' . $motif,
        ]);

        return $paiement;
    }

    /**
     * Crée un paiement avec saisie manuelle (FALLBACK)
     * À utiliser seulement si l'OCR échoue
     *
     * @deprecated Utiliser createPaymentWithOcr de préférence
     */
    public function createPayment(
        string $concoursId,
        string $reference,
        float $montant,
        UploadedFile $preuve
    ): Paiement {
        return DB::transaction(function () use ($concoursId, $reference, $montant, $preuve) {
            $existant = Paiement::where('reference', $reference)
                ->where('concours_id', $concoursId)
                ->first();

            if ($existant) {
                throw new \Exception('Cette référence de paiement est déjà utilisée');
            }

            $config = $this->concoursPaiementService->getConfiguration($concoursId);
            if (!$config || !$config->est_actif) {
                throw new \Exception('Configuration de paiement non disponible pour ce concours');
            }

            $path = $preuve->store('paiements', 'public');

            $ocrData = null;
            $statut = StatutPaiement::PENDING;

            try {
                $fullPath = Storage::disk('public')->path($path);
                $ocrData = $this->ocrService->extractReceiptData($fullPath);
            } catch (\Exception $e) {
                Log::warning("OCR failed for payment: {$e->getMessage()}");
            }

            $paiement = Paiement::create([
                'candidat_id' => null,
                'concours_id' => $concoursId,
                'reference' => $reference,
                'montant' => $montant,
                'preuve_paiement' => $path,
                'montant_ocr' => $ocrData->montant ?? null,
                'date_ocr' => $ocrData->date_paiement ?? null,
                'banque_ocr' => $ocrData->banque ?? null,
                'reference_ocr' => $ocrData->numero_recu ?? null,
                'ocr_confidence' => $ocrData->ocr_confidence ?? null,
                'ocr_raw_data' => $ocrData->raw_data ?? null,
                'statut' => $statut,
            ]);

            if ($ocrData) {
                $this->autoValidate($paiement);
            }

            return $paiement->fresh();
        });
    }

    /**
     * Auto-validation d'un paiement via OCR.
     * et diffère du numéro de reçu bancaire extrait par OCR.
     * Si OK → statut VERIFIED.
     *
     * @param Paiement $paiement Paiement à valider
     *
     * @return bool True si validé, False sinon
     */
    public function autoValidate(Paiement $paiement): bool
    {
        if ($paiement->statut === StatutPaiement::VERIFIED) {
            return true;
        }

        $config = $paiement->concours->configurationPaiement;
        if (!$config) {
            return false;
        }

        // Utiliser le validator modulaire pour la validation OCR
        $validator = new \App\Services\Payment\Validators\PaymentOcrValidator();
        return $validator->autoValidate($paiement, $config);
    }

    /**
     * Validation manuelle d'un paiement par un administrateur.
     *
     * @param int $paiementId ID du paiement
     * @param int $userId ID de l'utilisateur qui valide
     *
     * @return Paiement
     *
     * @throws \Exception Si le paiement n'existe pas ou ne peut pas être validé
     */
    public function manualValidate(int $paiementId, int $userId): Paiement
    {
        $paiement = Paiement::findOrFail($paiementId);


        if (!in_array($paiement->statut, [StatutPaiement::PENDING, StatutPaiement::PENDING_MANUAL_REVIEW])) {
            throw new \Exception('Ce paiement ne peut pas être validé manuellement');
        }


        $paiement->update([
            'statut' => StatutPaiement::VERIFIED,
            'validated_at' => now(),
            'validated_by' => $userId,
            'validation_notes' => ($paiement->validation_notes ? $paiement->validation_notes . '; ' : '') . 'Validé manuellement par l\'administrateur',
        ]);

        return $paiement;
    }
}
