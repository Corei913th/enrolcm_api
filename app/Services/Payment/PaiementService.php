<?php

namespace App\Services\Payment;

use App\Models\Paiement;
use App\Models\PaymentReference;
use App\Models\Candidature;
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
     * Upload preuve → OCR → Sauvegarde → Auto-validation
     */
    public function creerPaiement(
        string $candidatId,
        string $concoursId,
        string $reference,
        float $montant,
        UploadedFile $preuve
    ): Paiement {
        return DB::transaction(function () use ($candidatId, $concoursId, $reference, $montant, $preuve) {
            // Vérifier unicité
            $existant = Paiement::where('candidat_id', $candidatId)
                ->where('concours_id', $concoursId)
                ->first();
                
            if ($existant) {
                throw new \Exception('Un paiement existe déjà pour ce concours');
            }

            // Stocker preuve
            $path = $preuve->store('paiements');

            // Extraire OCR
            $ocrData = null;
            $statut = StatutPaiement::EN_ATTENTE;
            
            try {
                $fullPath = Storage::path($path);
                $ocrData = $this->ocrService->extractReceiptData($fullPath);
                $statut = StatutPaiement::OCR_VERIFIE;
            } catch (\Exception $e) {
                \Log::warning("OCR échoué pour paiement: {$e->getMessage()}");
            }

            // Créer paiement
            $paiement = Paiement::create([
                'candidat_id' => $candidatId,
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
            if ($statut === StatutPaiement::OCR_VERIFIE) {
                $this->autoValider($paiement);
            }

            return $paiement->fresh();
        });
    }

    /**
     * Auto-validation
     */
    public function autoValider(Paiement $paiement): bool
    {
        if ($paiement->statut !== StatutPaiement::OCR_VERIFIE) {
            return false;
        }

        $config = $paiement->concours->configurationPaiement;
        if (!$config) {
            return false;
        }

        $referenceValide = $this->verifierReference($paiement);
        $montantValide = $this->verifierMontant($paiement, $config->montant);
        $dateValide = $this->verifierDate($paiement, $config->date_limite);

        if ($referenceValide && $montantValide && $dateValide) {
            $this->valider($paiement->id, 'system');
            return true;
        }

        return false;
    }

    /**
     * Validation manuelle (agent)
     */
    public function valider(string $paiementId, string $userId): Paiement
    {
        return DB::transaction(function () use ($paiementId, $userId) {
            $paiement = Paiement::findOrFail($paiementId);

            if ($paiement->isValide()) {
                throw new \Exception('Ce paiement est déjà validé');
            }

            $paiement->valider($userId);

            $pru = PaymentReference::where('reference', $paiement->reference)
                ->where('candidat_id', $paiement->candidat_id)
                ->where('concours_id', $paiement->concours_id)
                ->first();

            if ($pru) {
                $pru->marquerUtilise();
            }

            // Créer inscription CONFIRMEE automatiquement
            $this->creerInscriptionConfirmee($paiement);

            return $paiement->fresh();
        });
    }

    /**
     * Rejet manuel (agent)
     */
    public function rejeter(string $paiementId, string $motif, string $userId): Paiement
    {
        return DB::transaction(function () use ($paiementId, $motif, $userId) {
            $paiement = Paiement::findOrFail($paiementId);

            if ($paiement->isValide()) {
                throw new \Exception('Impossible de rejeter un paiement validé');
            }

            $paiement->rejeter($motif, $userId);
            $this->invaliderInscription($paiement);

            return $paiement->fresh();
        });
    }

    private function verifierReference(Paiement $paiement): bool
    {
        $pru = PaymentReference::where('reference', $paiement->reference)
            ->where('candidat_id', $paiement->candidat_id)
            ->where('concours_id', $paiement->concours_id)
            ->first();

        return $pru && $pru->isValide();
    }

    private function verifierMontant(Paiement $paiement, float $montantAttendu): bool
    {
        $tolerance = 0.05;
        $min = $montantAttendu * (1 - $tolerance);
        $max = $montantAttendu * (1 + $tolerance);
        $montant = $paiement->montant_ocr ?? $paiement->montant;

        return $montant >= $min && $montant <= $max;
    }

    private function verifierDate(Paiement $paiement, $dateLimite): bool
    {
        $date = $paiement->date_ocr ?? $paiement->created_at;
        return $date <= $dateLimite;
    }

    private function creerInscriptionConfirmee(Paiement $paiement): void
    {
        $existante = Candidature::where('candidat_id', $paiement->candidat_id)
            ->where('concours_id', $paiement->concours_id)
            ->first();

        if ($existante) {
            $existante->confirmer();
        } else {
            Candidature::create([
                'candidat_id' => $paiement->candidat_id,
                'concours_id' => $paiement->concours_id,
                'statut_inscription' => StatutInscription::CONFIRMEE,
                'date_candidature' => now(),
                'date_inscription' => now(),
            ]);
        }
    }

    private function invaliderInscription(Paiement $paiement): void
    {
        $candidature = Candidature::where('candidat_id', $paiement->candidat_id)
            ->where('concours_id', $paiement->concours_id)
            ->first();

        if ($candidature) {
            $candidature->invalider();
        }
    }

    public function getPaiements(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Paiement::with(['candidat.utilisateur', 'concours', 'validatedBy']);

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

    public function getPaiementsEnException(int $perPage = 20): LengthAwarePaginator
    {
        return Paiement::with(['candidat.utilisateur', 'concours'])
            ->ocrVerifie()
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }
}
