<?php

namespace App\Services\Concours;

use App\Models\ConcoursPaiement;
use App\Models\Concours;
use App\DTOs\Concours\ConfigurePaymentDTO;
use App\Exceptions\ConcoursException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConcoursPaiementService
{
    /**
     * Configurer ou mettre à jour le paiement d’un concours.
     *
     * @param string $concoursId ID du concours
     * @param ConfigurePaymentDTO $dto DTO contenant les informations de configuration
     *
     * @return ConcoursPaiement Configuration créée ou mise à jour
     *
     * @throws ConcoursException Si le concours est introuvable, montant invalide ou date limite incorrecte
     */
    public function configurePayment(string $concoursId, ConfigurePaymentDTO $dto): ConcoursPaiement
    {
        try {
            $concours = Concours::findOrFail($concoursId);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($concoursId);
        }

        if ($dto->montant <= 0) {
            throw ConcoursException::invalidMontant();
        }

        if ($dto->date_limite >= $concours->date_limite_depot) {
            throw ConcoursException::invalidDateLimite();
        }

        return DB::transaction(function () use ($concoursId, $dto) {
            $config = ConcoursPaiement::where('concours_id', $concoursId)->first();

            if ($config) {
                $config->update($dto->toArray());
                return $config->fresh();
            }

            return ConcoursPaiement::create(array_merge($dto->toArray(), [
                'concours_id' => $concoursId,
            ]));
        });
    }

    /**
     * Récupérer la configuration de paiement d’un concours.
     *
     * @param string $concoursId ID du concours
     *
     * @return ConcoursPaiement Configuration trouvée
     *
     * @throws ConcoursException Si aucune configuration n’est définie
     */
    public function getConfiguration(string $concoursId): ConcoursPaiement
    {
        $config = ConcoursPaiement::where('concours_id', $concoursId)->first();

        if (!$config) {
            throw ConcoursException::paiementNotConfigured($concoursId);
        }

        return $config;
    }

    /**
     * Récupérer toutes les configurations actives et non expirées.
     *
     * @return Collection Liste des configurations actives
     */
    public function getActiveConfigurations(): Collection
    {
        return ConcoursPaiement::with('concours')
            ->actif()
            ->nonExpire()
            ->get();
    }

    /**
     * Désactiver une configuration de paiement.
     *
     * @param string $configId ID de la configuration
     *
     * @return ConcoursPaiement Configuration mise à jour
     *
     * @throws \Exception Si la configuration est introuvable
     */
    public function deactivate(string $configId): ConcoursPaiement
    {
        try {
            $config = ConcoursPaiement::findOrFail($configId);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Payment configuration not found.", 404);
        }

        $config->update(['est_actif' => false]);
        return $config->fresh();
    }

    /**
     * Activer une configuration de paiement.
     *
     * @param string $configId ID de la configuration
     *
     * @return ConcoursPaiement Configuration mise à jour
     *
     * @throws \Exception Si la configuration est introuvable
     */
    public function activate(string $configId): ConcoursPaiement
    {
        try {
            $config = ConcoursPaiement::findOrFail($configId);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Payment configuration not found.", 404);
        }

        $config->update(['est_actif' => true]);
        return $config->fresh();
    }

    /**
     * Vérifier si un concours possède une configuration de paiement valide.
     *
     * @param string $concoursId ID du concours
     *
     * @return bool True si configuration valide, False sinon
     */
    public function hasValidConfiguration(string $concoursId): bool
    {
        try {
            $config = $this->getConfiguration($concoursId);
            return $config->est_actif && !$config->isExpire();
        } catch (ConcoursException $e) {
            return false;
        }
    }

    /**
     * Obtenir les informations de paiement d’un concours.
     *
     * @param string $concoursId ID du concours
     *
     * @return array Tableau contenant montant, banque, numéro de compte, bénéficiaire, date limite et instructions
     *
     * @throws ConcoursException Si aucune configuration active n’est trouvée
     */
    public function getPaymentInfo(string $concoursId): array
    {
        $config = ConcoursPaiement::where('concours_id', $concoursId)->first();

        if (!$config || !$config->est_actif) {
            throw ConcoursException::paiementNotConfigured($concoursId);
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
     * Étendre la date limite de paiement.
     *
     * @param string $configId ID de la configuration
     * @param int $days Nombre de jours à ajouter
     *
     * @return ConcoursPaiement Configuration mise à jour
     *
     * @throws \Exception Si la configuration est introuvable
     */
    public function extendDeadline(string $configId, int $days): ConcoursPaiement
    {
        try {
            $config = ConcoursPaiement::findOrFail($configId);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Payment configuration not found.", 404);
        }

        $newDeadline = $config->date_limite->addDays($days);
        $config->update(['date_limite' => $newDeadline]);

        return $config->fresh();
    }
}
