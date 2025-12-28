<?php

namespace App\Services\Payment;

use App\Models\PaymentReference;
use App\Models\Candidat;
use App\Models\Concours;
use Illuminate\Support\Facades\DB;

class PaymentReferenceService
{
    /**
     * Générer une PRU pour un candidat et un concours
     */
    public function genererPRU(string $candidatId, string $concoursId, int $dureeValiditeJours = 30): PaymentReference
    {
        return DB::transaction(function () use ($candidatId, $concoursId, $dureeValiditeJours) {
            // Vérifier si une PRU valide existe déjà
            $pruExistante = PaymentReference::where('candidat_id', $candidatId)
                ->where('concours_id', $concoursId)
                ->valide()
                ->first();

            if ($pruExistante) {
                return $pruExistante;
            }

            // Générer une nouvelle référence unique
            $reference = $this->genererReferenceUnique($concoursId, $candidatId);

            return PaymentReference::create([
                'concours_id' => $concoursId,
                'candidat_id' => $candidatId,
                'reference' => $reference,
                'expires_at' => now()->addDays($dureeValiditeJours),
            ]);
        });
    }

    /**
     * Générer une référence unique
     */
    private function genererReferenceUnique(string $concoursId, string $candidatId): string
    {
        do {
            $reference = PaymentReference::genererReference($concoursId, $candidatId);
        } while (PaymentReference::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Vérifier si une PRU est valide
     */
    public function verifierPRU(string $reference): ?PaymentReference
    {
        $pru = PaymentReference::where('reference', $reference)->first();

        if (!$pru) {
            return null;
        }

        if (!$pru->isValide()) {
            return null;
        }

        return $pru;
    }

    /**
     * Renouveler une PRU expirée
     */
    public function renouvelerPRU(string $pruId, int $dureeValiditeJours = 30): PaymentReference
    {
        return DB::transaction(function () use ($pruId, $dureeValiditeJours) {
            $pru = PaymentReference::findOrFail($pruId);

            if ($pru->isUtilise()) {
                throw new \Exception('Cette référence a déjà été utilisée');
            }

            $pru->update([
                'expires_at' => now()->addDays($dureeValiditeJours),
            ]);

            return $pru->fresh();
        });
    }

    /**
     * Récupérer la PRU d'un candidat pour un concours
     */
    public function getPRUCandidatConcours(string $candidatId, string $concoursId): ?PaymentReference
    {
        return PaymentReference::where('candidat_id', $candidatId)
            ->where('concours_id', $concoursId)
            ->first();
    }

    /**
     * Récupérer toutes les PRU d'un candidat
     */
    public function getPRUCandidat(string $candidatId)
    {
        return PaymentReference::with('concours')
            ->where('candidat_id', $candidatId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Nettoyer les PRU expirées non utilisées
     */
    public function nettoyerPRUExpirees(): int
    {
        return PaymentReference::expire()
            ->whereNull('used_at')
            ->where('expires_at', '<', now()->subDays(90)) // Garder 90 jours d'historique
            ->delete();
    }

    /**
     * Statistiques des PRU
     */
    public function getStatistiques(?string $concoursId = null): array
    {
        $query = PaymentReference::query();

        if ($concoursId) {
            $query->where('concours_id', $concoursId);
        }

        return [
            'total' => $query->count(),
            'valides' => (clone $query)->valide()->count(),
            'expirees' => (clone $query)->expire()->count(),
            'utilisees' => (clone $query)->utilise()->count(),
            'taux_utilisation' => $this->calculerTauxUtilisation($query),
        ];
    }

    /**
     * Calculer le taux d'utilisation des PRU
     */
    private function calculerTauxUtilisation($query): float
    {
        $total = $query->count();
        if ($total === 0) {
            return 0;
        }

        $utilisees = (clone $query)->utilise()->count();

        return round(($utilisees / $total) * 100, 2);
    }
}
