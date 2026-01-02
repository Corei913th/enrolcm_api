<?php

namespace App\Services\Payment;

use App\Models\Paiement;
use App\Enums\StatutPaiement;
use App\Models\ConcoursPaiement;
use App\Services\OCR\TesseractOcrService;
use App\Services\Payment\ConcoursPaiementService;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class PaiementService
{
    public function __construct(
        private readonly TesseractOcrService $ocrService,
        private readonly ConcoursPaiementService $concoursPaiementService
    ) {}

    /**
     * Crée un paiement avec preuve uploadée et données OCR.
     *
     * Workflow : Upload preuve → OCR → Sauvegarde PENDING → Auto-validation.
     * IMPORTANT : candidat_id est NULL car le candidat n'existe pas encore.
     *
     * @param string $concoursId UUID du concours
     * @param string $reference Référence unique du paiement (PRU)
     * @param float $montant Montant payé
     * @param UploadedFile $preuve Fichier de preuve de paiement
     *
     * @return Paiement Paiement créé
     *
     * @throws \Exception Si la référence est déjà utilisée ou configuration inactive
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

        $referenceValide = $this->validatePaymentReference($paiement->reference);
        $montantValide = $this->verifyAmount($paiement, $config->montant);
        $dateValide = $this->verifyDate($paiement, $config->date_limite);
        $banqueValide = $this->verifyBank($paiement, $config);
        $numeroCompteValide = $this->verifyAccountNumber($paiement, $config);
        $confianceOcrValide = $this->verifyOcrConfidence($paiement, $config);


        if ($referenceValide && $montantValide && $dateValide && $banqueValide && $numeroCompteValide && $confianceOcrValide) {
            $paiement->update([
                'statut' => StatutPaiement::VERIFIED,
                'validated_at' => now(),
                'validated_by' => 'system',
            ]);
            return true;
        }

        // Vérifier si l'échec est seulement dû à des erreurs OCR mineures
        $hasOcrData = $paiement->montant_ocr || $paiement->banque_ocr || $paiement->numero_compte_ocr || $paiement->reference_ocr;

        if ($hasOcrData && $referenceValide && $confianceOcrValide) {

            $shouldMarkForManualReview = false;
            $validationNotes = [];


            if (!$montantValide && $paiement->montant_ocr) {
                $shouldMarkForManualReview = true;
                $validationNotes[] = 'Montant OCR proche du montant attendu';
            }


            if (!$numeroCompteValide && $paiement->numero_compte_ocr && $this->isMinorOcrError($paiement->numero_compte_ocr, $config->numero_compte)) {
                $shouldMarkForManualReview = true;
                $validationNotes[] = 'Erreur mineure détectée sur le numéro de compte OCR';
            }


            if (!$dateValide && $paiement->date_ocr) {
                $shouldMarkForManualReview = true;
                $validationNotes[] = 'Date OCR détectée mais hors limite';
            }


            if (!$banqueValide && $paiement->banque_ocr) {
                $shouldMarkForManualReview = true;
                $validationNotes[] = 'Banque OCR détectée mais non acceptée';
            }

            if ($shouldMarkForManualReview) {
                $paiement->update([
                    'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
                    'validation_notes' => implode('; ', $validationNotes),
                ]);
                return true; // Considéré comme "traité" mais nécessite validation manuelle
            }
        }

        // Échec réel - pas d'OCR ou erreurs majeures
        return false;
    }

    /**
     * Normalise un numéro de compte pour la comparaison.
     *
     * @param string|null $accountNumber
     *
     * @return string
     */
    private function normalizeAccountNumber(?string $accountNumber): string
    {
        if (!$accountNumber) {
            return '';
        }

        // Supprimer tous les espaces, tirets, points
        $normalized = preg_replace('/[\s\-\.]/', '', trim(strtoupper($accountNumber)));

        return $normalized;
    }

    /**
     * Détermine si l'erreur OCR sur le numéro de compte peut être considérée comme mineure.
     *
     * @param string $detected Numéro détecté par OCR
     * @param string $required Numéro requis par config
     *
     * @return bool
     */
    private function isMinorOcrError(string $detected, string $required): bool
    {
        $detected = preg_replace('/[\s\-\.]/', '', trim(strtoupper($detected)));
        $required = preg_replace('/[\s\-\.]/', '', trim(strtoupper($required)));

        // Même préfixe bancaire ?
        $detectedPrefix = $this->extractBankPrefix($detected);
        $requiredPrefix = $this->extractBankPrefix($required);

        if ($detectedPrefix !== $requiredPrefix) {
            return false; // Erreur majeure - mauvaise banque
        }

        // Calculer le nombre d'erreurs de chiffres
        $detectedNumbers = substr($detected, strlen($detectedPrefix));
        $requiredNumbers = substr($required, strlen($requiredPrefix));

        $errors = 0;
        $totalDigits = strlen($requiredNumbers);

        for ($i = 0; $i < min(strlen($detectedNumbers), $totalDigits); $i++) {
            if (isset($detectedNumbers[$i]) && $detectedNumbers[$i] !== $requiredNumbers[$i]) {
                $errors++;
            }
        }

        // Erreur mineure si ≤ 2 erreurs sur les chiffres (ex: 8→0, 1→7)
        return $errors <= 2;
    }

    /**
     * Extrait le préfixe bancaire d'un numéro de compte.
     *
     * @param string $accountNumber
     *
     * @return string
     */
    private function extractBankPrefix(string $accountNumber): string
    {
        // Patterns pour identifier les préfixes bancaires camerounais
        $patterns = [
            '/^(ECO)/i',      // Ecobank
            '/^(BICEC)/i',    // BICEC
            '/^(UBA)/i',      // UBA
            '/^(SGBC)/i',     // SGBC
            '/^(AFRILAND)/i', // Afriland
            '/^(SCB)/i',      // SCB
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $accountNumber, $matches)) {
                return strtoupper($matches[1]);
            }
        }

        // Si aucun préfixe connu, retourner les 3-4 premiers caractères
        return substr($accountNumber, 0, 4);
    }

    /**
     * Valide le format de la référence de paiement OCR.
     * @param string|null $reference Référence à valider
     *
     * @return bool True si la référence est valide
     */
    private function validatePaymentReference(?string $reference): bool
    {
        if (empty($reference)) {
            return false;
        }

        // Nettoyer la référence
        $reference = trim($reference);

        // Vérifier la longueur minimale (comme dans isPRUValid)
        if (strlen($reference) < 6) {
            return false;
        }

        // Vérifier le format (lettres, chiffres, tirets, points)
        if (!preg_match('/^[A-Z0-9\-_\.]+$/i', $reference)) {
            return false;
        }

        // Pour les références OCR, on accepte aussi les formats avec des espaces
        // mais on nettoie pour la validation
        $cleanReference = preg_replace('/\s+/', '', $reference);

        // Vérifier qu'il reste des caractères après nettoyage
        if (empty($cleanReference) || strlen($cleanReference) < 6) {
            return false;
        }

        return true;
    }

    /**
     * Validation manuelle par un agent.
     *
     * @param string $paiementId ID du paiement
     * @param string $userId ID de l'agent validateur
     *
     * @return Paiement Paiement validé
     *
     * @throws \Exception Si le paiement est déjà validé
     */
    public function manualValidate(string $paiementId, string $userId): Paiement
    {
        return DB::transaction(function () use ($paiementId, $userId) {
            $paiement = Paiement::findOrFail($paiementId);

            if ($paiement->statut === StatutPaiement::VERIFIED) {
                throw new \Exception('Ce paiement est déjà validé');
            }

            $paiement->update([
                'statut' => StatutPaiement::VERIFIED,
                'validated_at' => now(),
                'validated_by' => $userId,
            ]);

            return $paiement->fresh();
        });
    }

    /**
     * Rejet manuel d'un paiement par un agent.
     *
     * @param string $paiementId ID du paiement
     * @param string $motif Raison du rejet
     * @param string $userId ID de l'agent
     *
     * @return Paiement Paiement rejeté
     *
     * @throws \Exception Si le paiement est déjà validé
     */
    public function reject(string $paiementId, string $motif, string $userId): Paiement
    {
        return DB::transaction(function () use ($paiementId, $motif, $userId) {
            $paiement = Paiement::findOrFail($paiementId);

            if ($paiement->statut === StatutPaiement::VERIFIED) {
                throw new \Exception('Impossible de rejeter un paiement validé');
            }

            $paiement->update([
                'statut' => StatutPaiement::REJECTED,
                'rejection_reason' => $motif,
                'rejected_at' => now(),
                'rejected_by' => $userId,
            ]);

            return $paiement->fresh();
        });
    }

    /**
     * Vérifie si un PRU est valide et disponible.
     *
     * @param string $pru Référence du paiement
     * @param string $concoursId UUID du concours
     *
     * @return array Résultat de la validation
     */
    public function isPRUValid(string $pru, string $concoursId): array
    {
        $paiement = Paiement::where('reference', $pru)
            ->where('concours_id', $concoursId)
            ->where('statut', StatutPaiement::VERIFIED)
            ->whereNull('candidat_id')
            ->first();

        if (!$paiement) {
            return [
                'valid' => false,
                'message' => 'PRU invalide ou déjà utilisé'
            ];
        }

        $config = $paiement->concours->configurationPaiement;
        if ($config && $config->date_limite < now()) {
            return [
                'valid' => false,
                'message' => 'La date limite d\'inscription est dépassée'
            ];
        }

        return [
            'valid' => true,
            'concours' => $paiement->concours,
            'montant' => $paiement->montant
        ];
    }

    /**
     * Lie un paiement validé à un candidat.
     *
     * @param string $pru Référence du paiement
     * @param string $concoursId UUID du concours
     * @param string $candidatId ID du candidat
     *
     * @return Paiement Paiement mis à jour
     */
    public function linkToCandidat(string $pru, string $concoursId, string $candidatId): Paiement
    {
        $paiement = Paiement::where('reference', $pru)
            ->where('concours_id', $concoursId)
            ->where('statut', StatutPaiement::VERIFIED)
            ->whereNull('candidat_id')
            ->firstOrFail();

        $paiement->update(['candidat_id' => $candidatId]);

        return $paiement;
    }

    /**
     * Récupère la date de validation d'un paiement.
     *
     * @param string $pru Référence du paiement (PRU)
     * @param string $concoursId UUID du concours
     *
     * @return \DateTime|null Date de validation ou null si non validé
     */
    public function getValidationDate(string $pru, string $concoursId): ?DateTime
    {
        $paiement = Paiement::where('reference', $pru)
            ->where('concours_id', $concoursId)
            ->first();

        return $paiement?->validated_at;
    }

    /**
     * Vérifie si le montant OCR est dans la tolérance de ±5% du montant attendu.
     *
     * @param Paiement $paiement Paiement à vérifier
     * @param float $expectedAmount Montant attendu
     *
     * @return bool True si le montant est valide
     */
    private function verifyAmount(Paiement $paiement, float $expectedAmount): bool
    {
        $tolerance = 0.05; // ±5%
        $min = $expectedAmount * (1 - $tolerance);
        $max = $expectedAmount * (1 + $tolerance);
        $amount = $paiement->montant_ocr ?? $paiement->montant;

        return $amount >= $min && $amount <= $max;
    }

    /**
     * Vérifie si la date OCR est avant la date limite.
     *
     * @param Paiement $paiement Paiement à vérifier
     * @param \DateTime|string $deadline Date limite
     *
     * @return bool True si la date est valide
     */
    private function verifyDate(Paiement $paiement, $deadline): bool
    {
        $date = $paiement->date_ocr ?? $paiement->created_at;
        return $date <= $deadline;
    }

    /**
     * Vérifie si la banque détectée par OCR est dans la liste des banques acceptées.
     *
     * @param Paiement $paiement Paiement à vérifier
     * @param ConcoursPaiement $config Configuration de paiement
     *
     * @return bool True si la banque est acceptée ou si aucune liste n'est définie
     */
    private function verifyBank(Paiement $paiement, ConcoursPaiement $config): bool
    {
        $banqueOcr = $paiement->banque_ocr;

        // Si aucune banque détectée par OCR, on considère comme valide
        if (!$banqueOcr) {
            return true;
        }

        // Si aucune liste de banques acceptées, toutes les banques sont acceptées
        if (!$config->banques_acceptees) {
            return true;
        }

        return $this->concoursPaiementService->banqueEstAcceptee($config, $banqueOcr);
    }

    /**
     * Vérifie le numéro de compte du paiement OCR.
     *
     * @param Paiement $paiement
     * @param ConcoursPaiement $config
     *
     * @return bool
     */
    private function verifyAccountNumber(Paiement $paiement, ConcoursPaiement $config): bool
    {
        $numeroCompteOcr = $paiement->numero_compte_ocr;
        $numeroCompteConfig = $config->numero_compte;

        if (!$numeroCompteConfig) {
            return true;
        }


        if (!$numeroCompteOcr) {
            return false;
        }

        // Nettoyer les numéros de compte pour la comparaison
        $ocrClean = $this->normalizeAccountNumber($numeroCompteOcr);
        $configClean = $this->normalizeAccountNumber($numeroCompteConfig);

        // Comparaison exacte après normalisation
        if (strcasecmp($ocrClean, $configClean) === 0) {
            return true;
        }

        // Pour la tolérance OCR, vérifier si c'est une erreur mineure
        return $this->isMinorOcrError($numeroCompteOcr, $numeroCompteConfig);
    }

    /**
     * Vérifie si la confiance OCR est suffisante pour la validation automatique.
     *
     * @param Paiement $paiement Paiement à vérifier
     * @param ConcoursPaiement $config Configuration de paiement
     *
     * @return bool True si la confiance OCR est suffisante
     */
    private function verifyOcrConfidence(Paiement $paiement, ConcoursPaiement $config): bool
    {
        $confianceOcr = $paiement->ocr_confidence;

        // Si pas de données OCR, on ne peut pas valider automatiquement
        if (!$confianceOcr) {
            return false;
        }

        $seuilMinimum = $config->minimum_confiance_ocr ?? 85.00;

        return $confianceOcr >= $seuilMinimum;
    }

    /**
     * Récupère une liste paginée de paiements avec filtres.
     *
     * @param array $filters Tableau de filtres (statut, concours_id, candidat_id, reference)
     * @param int $perPage Nombre d'éléments par page
     *
     * @return LengthAwarePaginator Liste paginée des paiements
     */
    public function getPayments(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Paiement::with(['candidat', 'concours']);

        if (isset($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        if (isset($filters['concours_id'])) {
            $query->where('concours_id', $filters['concours_id']);
        }

        if (isset($filters['candidat_id'])) {
            $query->where('candidat_id', $filters['candidat_id']);
        }

        if (isset($filters['reference'])) {
            $query->where('reference', 'like', '%' . $filters['reference'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Récupère les paiements en attente (PENDING).
     *
     * @param int $perPage Nombre d'éléments par page
     *
     * @return LengthAwarePaginator Liste paginée des paiements en attente
     */
    public function getPendingPayments(int $perPage = 20): LengthAwarePaginator
    {
        return Paiement::with(['concours'])
            ->where('statut', StatutPaiement::PENDING)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }
}
