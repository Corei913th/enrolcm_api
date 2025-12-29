<?php

namespace App\Services\Payment;

use App\Models\Paiement;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\ConcoursPaiement;
use App\Enums\StatutPaiement;
use App\Enums\StatutInscription;
use App\Services\OCR\TesseractOcrService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaiementService
{
    public function __construct(
        private readonly TesseractOcrService $ocrService
    ) {}

    /**
     * WORKFLOW: Upload preuve → OCR → Sauvegarde PENDING → Auto-validation → Création candidat si VERIFIED
     * IMPORTANT: candidat_id est NULL car le candidat n'existe pas encore
     */
    public function createPayment(
        string $concoursId,
        string $reference,
        float $montant,
        UploadedFile $preuve
    ): Paiement {
        return DB::transaction(function () use ($concoursId, $reference, $montant, $preuve) {
            // Vérifier que le PRU n'est pas déjà utilisé
            $existant = Paiement::where('reference', $reference)
                ->where('concours_id', $concoursId)
                ->first();
                
            if ($existant) {
                throw new \Exception('Cette référence de paiement est déjà utilisée');
            }

            // Vérifier configuration paiement
            $config = ConcoursPaiement::where('concours_id', $concoursId)->first();
            if (!$config || !$config->est_actif) {
                throw new \Exception('Configuration de paiement non disponible pour ce concours');
            }

            // Stocker preuve
            $path = $preuve->store('paiements', 'public');

            // Extraire données OCR
            $ocrData = null;
            $statut = StatutPaiement::PENDING;
            
            try {
                $fullPath = Storage::disk('public')->path($path);
                $ocrData = $this->ocrService->extractReceiptData($fullPath);
            } catch (\Exception $e) {
                \Log::warning("OCR failed for payment: {$e->getMessage()}");
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

            // Auto-validation si OCR OK
            if ($ocrData) {
                $this->autoValidate($paiement);
            }

            return $paiement->fresh();
        });
    }

    /**
     * Auto-validation: vérifie référence + montant ±5% + date
     * Si OK → VERIFIED (mais PAS de création candidat ici)
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

        $referenceValide = $this->verifyReference($paiement);
        $montantValide = $this->verifyAmount($paiement, $config->montant);
        $dateValide = $this->verifyDate($paiement, $config->date_limite);

        if ($referenceValide && $montantValide && $dateValide) {
            $paiement->update([
                'statut' => StatutPaiement::VERIFIED,
                'validated_at' => now(),
                'validated_by' => 'system',
            ]);
            return true;
        }

        return false;
    }

    /**
     * Validation manuelle par agent
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
     * Rejet manuel par agent
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

            // Invalider candidature si elle existe
            if ($paiement->candidat_id) {
                $this->invalidateRegistration($paiement);
            }

            return $paiement->fresh();
        });
    }

    /**
     * Vérifier si un PRU est valide et disponible
     */
    public function verifyPRU(string $reference, string $concoursId): bool
    {
        $paiement = Paiement::where('reference', $reference)
            ->where('concours_id', $concoursId)
            ->where('statut', StatutPaiement::VERIFIED)
            ->whereNull('candidat_id')
            ->first();

        if (!$paiement) {
            return false;
        }

        // Vérifier date limite
        $config = $paiement->concours->configurationPaiement;
        if ($config && $config->date_limite < now()) {
            return false;
        }

        return true;
    }

    /**
     * Lier un paiement à un candidat après création du compte
     */
    public function linkToCandidat(string $reference, string $concoursId, string $candidatId): Paiement
    {
        return DB::transaction(function () use ($reference, $concoursId, $candidatId) {
            $paiement = Paiement::where('reference', $reference)
                ->where('concours_id', $concoursId)
                ->where('statut', StatutPaiement::VERIFIED)
                ->whereNull('candidat_id')
                ->firstOrFail();

            $paiement->update(['candidat_id' => $candidatId]);

            // Créer inscription ACTIF automatiquement
            $this->createActiveRegistration($paiement);

            return $paiement->fresh();
        });
    }

    private function verifyReference(Paiement $paiement): bool
    {
        // La référence OCR doit correspondre au PRU
        return $paiement->reference_ocr === $paiement->reference;
    }

    private function verifyAmount(Paiement $paiement, float $expectedAmount): bool
    {
        $tolerance = 0.05; // ±5%
        $min = $expectedAmount * (1 - $tolerance);
        $max = $expectedAmount * (1 + $tolerance);
        $amount = $paiement->montant_ocr ?? $paiement->montant;

        return $amount >= $min && $amount <= $max;
    }

    private function verifyDate(Paiement $paiement, $deadline): bool
    {
        $date = $paiement->date_ocr ?? $paiement->created_at;
        return $date <= $deadline;
    }

    private function createActiveRegistration(Paiement $paiement): void
    {
        $existing = Candidature::where('candidat_id', $paiement->candidat_id)
            ->where('concours_id', $paiement->concours_id)
            ->first();

        if (!$existing) {
            Candidature::create([
                'candidat_id' => $paiement->candidat_id,
                'concours_id' => $paiement->concours_id,
                'statut_inscription' => StatutInscription::ACTIF,
                'date_candidature' => now(),
                'date_inscription' => now(),
            ]);
        }
    }

    private function invalidateRegistration(Paiement $paiement): void
    {
        $candidature = Candidature::where('candidat_id', $paiement->candidat_id)
            ->where('concours_id', $paiement->concours_id)
            ->first();

        if ($candidature) {
            $candidature->update(['statut_inscription' => StatutInscription::INVALIDE]);
        }
    }

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

    public function getPendingPayments(int $perPage = 20): LengthAwarePaginator
    {
        return Paiement::with(['concours'])
            ->where('statut', StatutPaiement::PENDING)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }
}
