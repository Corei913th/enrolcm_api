<?php

namespace App\Services\Payment;

use App\Models\ConcoursPaiement;
use App\Models\Concours;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class ConcoursPaiementService
{
    /**
     * Créer ou mettre à jour la configuration de paiement d'un concours.
     *
     * @param string $concoursId UUID du concours
     * @param array $data Données de configuration (banque, compte, montant, date_limite, etc.)
     *
     * @return ConcoursPaiement Configuration créée ou mise à jour
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
     * Récupérer la configuration de paiement d'un concours.
     *
     * @param string $concoursId UUID du concours
     *
     * @return ConcoursPaiement|null Configuration ou null si inexistante
     */
    public function getConfiguration(string $concoursId): ?ConcoursPaiement
    {
        return ConcoursPaiement::where('concours_id', $concoursId)->first();
    }

    /**
     * Récupérer toutes les configurations actives et non expirées.
     *
     * @return Collection Liste des configurations actives
     */
    public function getConfigurationsActives(): Collection
    {
        return ConcoursPaiement::with('concours')
            ->actif()
            ->nonExpire()
            ->get();
    }

    /**
     * Désactiver la configuration de paiement d'un concours.
     *
     * @param string $configId ID de la configuration
     *
     * @return ConcoursPaiement Configuration mise à jour
     */
    public function desactiver(string $configId): ConcoursPaiement
    {
        $config = ConcoursPaiement::findOrFail($configId);
        $config->update(['est_actif' => false]);
        return $config->fresh();
    }

    /**
     * Activer la configuration de paiement d'un concours.
     *
     * @param string $configId ID de la configuration
     *
     * @return ConcoursPaiement Configuration mise à jour
     */
    public function activer(string $configId): ConcoursPaiement
    {
        $config = ConcoursPaiement::findOrFail($configId);
        $config->update(['est_actif' => true]);
        return $config->fresh();
    }

    /**
     * Vérifier si un concours a une configuration de paiement valide.
     *
     * @param string $concoursId UUID du concours
     *
     * @return bool True si configuration active et non expirée
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
     * Récupérer les configurations expirant bientôt.
     *
     * @param int $jours Nombre de jours avant expiration (par défaut 7)
     *
     * @return Collection Liste des configurations expirant bientôt
     */
    public function getConfigurationsExpirantBientot(int $jours = 7): Collection
    {
        $dateLimite = now()->addDays($jours);

        return ConcoursPaiement::with('concours')
            ->actif()
            ->where('date_limite', '<=', $dateLimite)
            ->where('date_limite', '>=', now())
            ->orderBy('date_limite', 'asc')
            ->get();
    }

    /**
     * Prolonger la date limite de paiement d'une configuration.
     *
     * @param string $configId ID de la configuration
     * @param int $jours Nombre de jours à ajouter
     *
     * @return ConcoursPaiement Configuration mise à jour
     */
    public function prolongerDateLimite(string $configId, int $jours): ConcoursPaiement
    {
        $config = ConcoursPaiement::findOrFail($configId);

        $nouvelleDateLimite = $config->date_limite->addDays($jours);

        $config->update(['date_limite' => $nouvelleDateLimite]);

        return $config->fresh();
    }

    /**
     * Statistiques globales des configurations de paiement.
     *
     * @return array Tableau des statistiques (total, actives, non_expirees, expirees, montant_moyen)
     */
    public function getStatistiques(): array
    {
        return [
            'total' => ConcoursPaiement::count(),
            'actives' => ConcoursPaiement::actif()->count(),
            'non_expirees' => ConcoursPaiement::nonExpire()->count(),
            'expirees' => ConcoursPaiement::where('date_limite', '<', now())->count(),
            'montant_moyen' => ConcoursPaiement::actif()->avg('montant'),
        ];
    }

    /**
     * Valider les données de configuration avant sauvegarde.
     *
     * @param array $data Données de configuration
     *
     * @return array Tableau des erreurs (vide si aucune erreur)
     */
    public function validerConfiguration(array $data): array
    {
        $errors = [];

        if (!isset($data['banque_nom']) || empty($data['banque_nom'])) {
            $errors['banque_nom'] = 'Le nom de la banque est obligatoire';
        }

        if (!isset($data['numero_compte']) || empty($data['numero_compte'])) {
            $errors['numero_compte'] = 'Le numéro de compte est obligatoire';
        }

        if (!isset($data['montant']) || $data['montant'] <= 0) {
            $errors['montant'] = 'Le montant doit être supérieur à 0';
        }

        if (!isset($data['date_limite']) || strtotime($data['date_limite']) < time()) {
            $errors['date_limite'] = 'La date limite doit être dans le futur';
        }

        return $errors;
    }
}