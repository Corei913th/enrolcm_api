<?php

namespace App\Services\Domain\Registration;

use App\Enums\StatutPaiement;
use App\Models\Concours;
use App\Models\Paiement;
use Carbon\Carbon;

class PaiementAutoValidatorService
{
    /**
     * Valider automatiquement un paiement
     */
    public function validate(Concours $concours, array $data): array
    {
        $checks = [];
        $config = $concours->configurationPaiement;

        // 1. Vérifier le montant
        $checks['montant'] = $this->checkMontant($concours, $data['montant'] ?? 0);

        // 2. Vérifier la date
        $checks['date'] = $this->checkDate($data['date_paiement'] ?? null, $concours);

        // 3. Vérifier le format de la référence
        // $checks['reference_format'] = $this->checkReferenceFormat($data['reference_paiement'] ?? null);

        // 4. Vérifier l'unicité de la référence
        $checks['reference_unique'] = $this->checkReferenceUniqueness($data['reference_paiement'] ?? null);

        // 5. Vérifier la banque (si config disposible)
        $checks['banque'] = $this->checkBanque($config, $data['banque'] ?? null);

        // 6. Vérifier le numéro de compte (si config disponible)
        $checks['numero_compte'] = $this->checkNumeroCompte($config, $data['numero_compte'] ?? null);

        // Déterminer le statut
        $allValid = collect($checks)->every(fn ($c) => $c['valide']);
        $statut = $allValid ? StatutPaiement::VERIFIED : StatutPaiement::PENDING;

        // Construire les raisons si non valide
        $raisons = collect($checks)
            ->filter(fn ($c) => ! $c['valide'])
            ->map(fn ($c) => $c['raison'])
            ->filter()
            ->values()
            ->toArray();

        return [
            'statut' => $statut,
            'auto_valide' => $allValid,
            'checks' => $checks,
            'raisons_attente' => $raisons,
        ];
    }

    /**
     * Vérifier le montant (tolérance ±5%)
     */
    private function checkMontant(Concours $concours, $montant): array
    {
        $montant = (float) $montant;
        $montantAttendu = (float) ($concours->configurationPaiement?->montantTotal() ?? $concours->frais_inscription);
        $tolerance = $montantAttendu * 0.05; // 5%

        $valide = abs($montant - $montantAttendu) <= $tolerance;

        return [
            'valide' => $valide,
            'valeur' => $montant,
            'attendu' => $montantAttendu,
            'raison' => $valide ? null : "Montant incorrect (attendu: {$montantAttendu} FCFA, reçu: {$montant} FCFA)",
        ];
    }

    /**
     * Vérifier la date (pas dans le futur, pas trop ancien, avant la date limite)
     */
    private function checkDate(?string $datePaiement, Concours $concours): array
    {
        if (! $datePaiement) {
            return ['valide' => false, 'raison' => 'Date de paiement manquante'];
        }

        try {
            $date = Carbon::parse($datePaiement);
            $now = Carbon::now();
            $dateLimite = $concours->date_limite_depot;

            // Pas dans le futur
            if ($date->isFuture()) {
                return [
                    'valide' => false,
                    'raison' => 'La date de paiement ne peut pas être dans le futur',
                ];
            }

            // Avant la date limite de dépôt
            if ($dateLimite && $date->greaterThan($dateLimite->endOfDay())) {
                $fmtDate = $date->format('d/m/Y');
                $fmtLimite = $dateLimite->format('d/m/Y');

                return [
                    'valide' => false,
                    'raison' => "La date de paiement ($fmtDate) est postérieure à la date limite de dépôt ($fmtLimite)",
                ];
            }

            // Pas trop ancien (max 60 jours par défaut, sauf si c'est pour un concours très long)
            if ($date->diffInDays($now) > 60) {
                return [
                    'valide' => false,
                    'raison' => 'La date de paiement est trop ancienne (max 60 jours)',
                ];
            }

            return ['valide' => true, 'raison' => null];
        } catch (\Exception $e) {
            return ['valide' => false, 'raison' => 'Format de date invalide'];
        }
    }

    /**
     * Vérifier le format de la référence (PRU)
     */
    private function checkReferenceFormat(?string $reference): array
    {
        if (! $reference) {
            return [
                'valide' => false,
                'raison' => 'Référence de paiement manquante',
            ];
        }

        // Format attendu: XXX-YYYY-NNNNN (ex: POLY-2025-48392) ou RCPxxxxxxxx
        $valide = preg_match('/^[A-Z]+-\d{4}-\d{5}$/', $reference) || preg_match('/^RCP\d{7,}/i', $reference);

        return [
            'valide' => $valide,
            'raison' => $valide ? null : 'Format de référence invalide',
        ];
    }

    /**
     * Vérifier l'unicité de la référence
     */
    private function checkReferenceUniqueness(?string $reference): array
    {
        if (! $reference) {
            return ['valide' => false, 'raison' => 'Référence manquante'];
        }

        $exists = Paiement::where('reference', $reference)->exists();

        return [
            'valide' => ! $exists,
            'raison' => $exists ? 'Cette référence de paiement a déjà été utilisée pour une inscription' : null,
        ];
    }

    /**
     * Vérifier si la banque est acceptée
     */
    private function checkBanque($config, ?string $banque): array
    {
        if (! $config) {
            return ['valide' => true, 'raison' => null];
        }

        $allowed = [];
        if (! empty($config->banque_nom)) {
            $allowed[] = strtoupper(trim($config->banque_nom));
        }
        if ($config->banques_acceptees) {
            foreach ($config->banques_acceptees as $b) {
                $allowed[] = strtoupper(trim($b));
            }
        }

        $allowed = array_unique($allowed);

        if (empty($allowed)) {
            return ['valide' => true, 'raison' => null]; // Pas de restriction
        }

        if (! $banque) {
            return ['valide' => false, 'raison' => 'Banque non détectée'];
        }

        $banque = strtoupper(trim($banque));
        $valide = false;

        foreach ($allowed as $item) {
            if (str_contains($banque, $item) || str_contains($item, $banque)) {
                $valide = true;
                break;
            }
        }

        return [
            'valide' => $valide,
            'raison' => $valide ? null : "La banque '{$banque}' n'est pas acceptée pour ce concours. Banques attendues: " . implode(', ', $allowed),
        ];
    }

    /**
     * Vérifier le numéro de compte
     */
    private function checkNumeroCompte($config, ?string $numeroCompte): array
    {
        if (! $config || ! $config->numero_compte) {
            return ['valide' => true, 'raison' => null];
        }

        if (! $numeroCompte) {
            return ['valide' => false, 'raison' => 'Numéro de compte destinataire non détecté'];
        }

        // Normalisation basique pour comparaison
        $cleanDetected = preg_replace('/[^A-Z0-9]/', '', strtoupper($numeroCompte));
        $cleanConfig = preg_replace('/[^A-Z0-9]/', '', strtoupper($config->numero_compte));

        $valide = str_contains($cleanDetected, $cleanConfig) || str_contains($cleanConfig, $cleanDetected);

        return [
            'valide' => $valide,
            'raison' => $valide ? null : "Le numéro de compte sur le reçu ne correspond pas au compte de l'école (Attendu: {$config->numero_compte})",
        ];
    }
}
