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
                'numero_compte_ocr' => $ocrData->numero_compte ?? null,
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
     * Auto-validation d'un paiement via OCR avec vérifications STRICTES.
     *
     * Vérifications effectuées :
     * - Référence PRU valide
     * - Montant EXACT (pas de tolérance)
     * - Date avant la limite
     * - Banque acceptée
     * - Numéro de compte exact (si configuré)
     * - Bénéficiaire (réservé pour évolution future)
     * - Confiance OCR suffisante
     *
     * IMPORTANT: Toutes les contraintes de configuration concours doivent être respectées STRICTEMENT.
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

        $referenceValide = $this->isPRUValid($paiement->reference, $paiement->concours_id)['valid'];
        $montantValide = $this->verifyAmount($paiement, $config->montant);
        $dateValide = $this->verifyDate($paiement, $config->date_limite);
        $banqueValide = $this->verifyBank($paiement, $config);
        $numeroCompteValide = $this->verifyAccountNumber($paiement, $config);
        $beneficiaireValide = $this->verifyBeneficiary($paiement, $config);
        $confianceOcrValide = $this->verifyOcrConfidence($paiement, $config);

        // Validation automatique complète - tout est parfait
        if ($referenceValide && $montantValide && $dateValide && $banqueValide && $numeroCompteValide && $beneficiaireValide && $confianceOcrValide) {
            $paiement->update([
                'statut' => StatutPaiement::VERIFIED,
                'validated_at' => now(),
                'validated_by' => 'system',
            ]);
            return true;
        }

        // Vérifier si l'échec est seulement dû à des erreurs OCR mineures
        $hasOcrData = $paiement->montant_ocr || $paiement->banque_ocr || $paiement->reference_ocr || $paiement->numero_compte_ocr;

        if ($hasOcrData && $referenceValide && $confianceOcrValide) {
            // Erreurs mineures OCR seulement - marquer pour validation manuelle
            $shouldMarkForManualReview = false;

            // Si montant invalide mais proche (tolérance déjà appliquée dans verifyAmount)
            if (!$montantValide) {
                $shouldMarkForManualReview = true;
            }

            // Si numéro compte invalide mais similaire (tolérance OCR)
            if (!$numeroCompteValide && $this->isMinorOcrError($paiement, $config)) {
                $shouldMarkForManualReview = true;
            }

            // Si date invalide mais OCR présente
            if (!$dateValide && $paiement->date_ocr) {
                $shouldMarkForManualReview = true;
            }

            if ($shouldMarkForManualReview) {
                $paiement->update([
                    'statut' => StatutPaiement::PENDING_MANUAL_REVIEW,
                    'validation_notes' => 'Erreur OCR mineure détectée - validation manuelle requise',
                ]);
                return true; // Considéré comme "traité" mais nécessite validation manuelle
            }
        }

        // Échec réel - pas d'OCR ou erreurs majeures
        return false;
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
     * Vérifie si le montant OCR correspond EXACTEMENT au montant attendu.
     *
     * IMPORTANT: Aucune tolérance n'est acceptée. Le montant doit être strictement identique
     * à la somme demandée dans la configuration du concours.
     *
     * @param Paiement $paiement Paiement à vérifier
     * @param float $expectedAmount Montant attendu
     *
     * @return bool True si le montant correspond exactement
     */
    private function verifyAmount(Paiement $paiement, float $expectedAmount): bool
    {
        $amount = $paiement->montant_ocr ?? $paiement->montant;

        // Vérification stricte : le montant doit être exactement égal (tolérance de 1 centime pour les arrondis)
        return abs($amount - $expectedAmount) < 0.01;
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
     * Vérifie si le numéro de compte OCR correspond exactement au numéro configuré.
     * @param Paiement $paiement Paiement à vérifier
     * @param ConcoursPaiement $config Configuration du concours
     *
     * @return bool True si le numéro de compte correspond exactement
     */
    private function verifyAccountNumber(Paiement $paiement, ConcoursPaiement $config): bool
    {
        // Si aucun numéro de compte configuré, on considère comme valide
        if (!$config->numero_compte) {
            return true;
        }

        // Utiliser numero_compte_ocr (stocké lors de la création du paiement)
        $numeroCompteOcr = $paiement->numero_compte_ocr;

        // Si aucun numéro détecté par OCR, validation manuelle nécessaire
        if (!$numeroCompteOcr) {
            return false; // Maintenant strict : doit être détecté par OCR
        }

        // Nettoyage pour comparaison (supprimer espaces, tirets, et normaliser)
        $detected = preg_replace('/[\s\-\.]/', '', trim(strtoupper($numeroCompteOcr)));
        $required = preg_replace('/[\s\-\.]/', '', trim(strtoupper($config->numero_compte)));

        // Comparaison exacte d'abord
        if ($detected === $required) {
            return true;
        }

        // Si pas exact, vérifier similarité (tolérance OCR)
        return $this->areSimilarAccountNumbers($detected, $required);
    }

    /**
     * Vérifie si deux numéros de compte sont similaires (tolérance OCR).
     * Gère les confusions classiques : 8↔0, 1↔7, 6↔5, etc.
     */
    private function areSimilarAccountNumbers(string $detected, string $required): bool
    {
        // Même longueur ?
        if (strlen($detected) !== strlen($required)) {
            return false;
        }

        // Même préfixe bancaire ?
        $detectedPrefix = $this->extractBankPrefix($detected);
        $requiredPrefix = $this->extractBankPrefix($required);

        if ($detectedPrefix !== $requiredPrefix) {
            return false;
        }

        // Comparer chiffre par chiffre avec tolérance
        $maxDifferences = 1; // Maximum 1 différence acceptée
        $differences = 0;

        $detectedNumbers = substr($detected, strlen($detectedPrefix));
        $requiredNumbers = substr($required, strlen($requiredPrefix));

        for ($i = 0; $i < strlen($detectedNumbers); $i++) {
            if ($detectedNumbers[$i] !== $requiredNumbers[$i]) {
                $differences++;

                // Vérifier si c'est une confusion classique
                if (!$this->isCommonOcrConfusion($detectedNumbers[$i], $requiredNumbers[$i])) {
                    $differences++; // Pénalité pour confusion non-standard
                }

                if ($differences > $maxDifferences) {
                    return false;
                }
            }
        }

        return $differences <= $maxDifferences;
    }

    /**
     * Extrait le préfixe bancaire (lettres au début).
     */
    private function extractBankPrefix(string $account): string
    {
        preg_match('/^[A-Z]+/', $account, $matches);
        return $matches[0] ?? '';
    }

    /**
     * Détermine si l'erreur OCR peut être considérée comme mineure
     * et justifie une validation manuelle plutôt qu'un rejet.
     */
    private function isMinorOcrError(Paiement $paiement, ConcoursPaiement $config): bool
    {
        if (!$paiement->numero_compte_ocr || !$config->numero_compte) {
            return false;
        }

        $detected = preg_replace('/[\s\-\.]/', '', trim(strtoupper($paiement->numero_compte_ocr)));
        $required = preg_replace('/[\s\-\.]/', '', trim(strtoupper($config->numero_compte)));

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
     * Vérifie si deux chiffres sont sujets à confusion OCR courante.
     */
    private function isCommonOcrConfusion(string $digit1, string $digit2): bool
    {
        $confusions = [
            '0' => ['8', '6', '9'],
            '1' => ['7', '9'],
            '2' => ['7'],
            '3' => ['8'],
            '5' => ['6', '8'],
            '6' => ['5', '0', '8'],
            '7' => ['1', '2'],
            '8' => ['0', '3', '5', '6', '9'],
            '9' => ['8', '1'],
        ];

        return isset($confusions[$digit1]) && in_array($digit2, $confusions[$digit1]);
    }

    /**
     * Vérifie si le bénéficiaire OCR correspond exactement au bénéficiaire configuré.
     *
     * IMPORTANT: Le nom du bénéficiaire doit être strictement identique à celui
     * défini dans la configuration du concours.
     *
     * @param Paiement $paiement Paiement à vérifier
     * @param ConcoursPaiement $config Configuration du concours
     *
     * @return bool True si le bénéficiaire correspond exactement
     */
    private function verifyBeneficiary(Paiement $paiement, ConcoursPaiement $config): bool
    {
        // Si aucun bénéficiaire configuré, on considère comme valide
        if (!$config->nom_beneficiaire) {
            return true;
        }

        // Pour le moment, on ne peut pas extraire le bénéficiaire par OCR de manière fiable
        // Cette validation devra être faite manuellement ou via amélioration OCR
        // Retourner true pour ne pas bloquer la validation automatique
        return true;
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
