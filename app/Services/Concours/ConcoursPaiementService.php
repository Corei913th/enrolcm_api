<?php

namespace App\Services\Concours;

use App\Models\ConcoursPaiement;
use App\Models\Concours;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class ConcoursPaiementService
{
    /**
     * Configurer le paiement d'un concours
     */
    public function configurerPaiement(string $concoursId, array $data): ConcoursPaiement
    {
        return DB::transaction(function () use ($concoursId, $data) {
            $config = ConcoursPaiement::where('concours_id', $concoursId)->first();

            if ($config) {
                $config->update($data);
                return $config->fresh();
            }

            return ConcoursPaiement::create(array_merge($data, [
                'concours_id' => $concoursId,
            ]));
        });
    }

    /**
     * Récupérer la configuration de paiement d'un concours
     */
    public function getConfiguration(string $concoursId): ?ConcoursPaiement
    {
        return ConcoursPaiement::where('concours_id', $concoursId)->first();
    }

    /**
     * Récupérer toutes les configurations actives
     */
    public function getConfigurationsActives(): Collection
    {
        return ConcoursPaiement::with('concours')
            ->actif()
            ->nonExpire()
            ->get();
    }

    /**
     * Désactiver la configuration
     */
    public function desactiver(string $configId): ConcoursPaiement
    {
        $config = ConcoursPaiement::findOrFail($configId);
        $config->update(['est_actif' => false]);
        return $config->fresh();
    }

    /**
     * Activer la configuration
     */
    public function activer(string $configId): ConcoursPaiement
    {
        $config = ConcoursPaiement::findOrFail($configId);
        $config->update(['est_actif' => true]);
        return $config->fresh();
    }

    /**
     * Vérifier si un concours a une configuration valide
     */
    public function hasConfigurationValide(string $concoursId): bool
    {
        $config = $this->getConfiguration($concoursId);

        if (!$config) {
            return false;
        }

        return $config->est_actif && !$config->isExpire();
    }

    /**
     * Récupérer les informations de paiement pour un concours (API publique)
     */
    public function getInfosPaiement(string $concoursId): array
    {
        $config = $this->getConfiguration($concoursId);

        if (!$config || !$config->est_actif) {
            throw new \Exception('Configuration de paiement non disponible pour ce concours');
        }

        return [
            'montant' => $config->montant,
            'banque' => $config->banque_nom,
            'numero_compte' => $config->numero_compte,
            'nom_beneficiaire' => $config->nom_beneficiaire,
            'date_limite' => $config->date_limite,
            'instructions' => $config->instructions,
        ];
    }

    /**
     * Prolonger la date limite
     */
    public function prolongerDateLimite(string $configId, int $jours): ConcoursPaiement
    {
        $config = ConcoursPaiement::findOrFail($configId);
        
        $nouvelleDateLimite = $config->date_limite->addDays($jours);
        
        $config->update(['date_limite' => $nouvelleDateLimite]);
        
        return $config->fresh();
    }
}
