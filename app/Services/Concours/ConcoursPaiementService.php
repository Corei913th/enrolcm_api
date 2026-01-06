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
     * Vérifier si une banque est acceptée pour cette configuration.
     *
     * @param string $concoursId ID du concours
     * @param string $nomBanque Nom de la banque à vérifier
     *
     * @return bool True si la banque est acceptée
     *
     * @throws ConcoursException Si aucune configuration n'est trouvée
     */
    public function banqueEstAcceptee(string $concoursId, string $nomBanque): bool
    {
        $config = $this->getConfiguration($concoursId);
        return $config->banqueEstAcceptee($nomBanque);
    }

    /**
     * Vérifier si la validation automatique est possible pour ce concours.
     *
     * @param string $concoursId ID du concours
     *
     * @return bool True si la validation auto est possible
     *
     * @throws ConcoursException Si aucune configuration n'est trouvée
     */
    public function peutValiderAutomatiquement(string $concoursId): bool
    {
        $config = $this->getConfiguration($concoursId);
        return $config->peutValiderAutomatiquement();
    }

    /**
     * Récupérer les informations bancaires complètes d'un concours.
     *
     * @param string $concoursId ID du concours
     *
     * @return array Informations bancaires formatées
     *
     * @throws ConcoursException Si aucune configuration n'est trouvée
     */
    public function getInformationsBancaires(string $concoursId): array
    {
        $config = $this->getConfiguration($concoursId);
        return $config->getInformationsBancaires();
    }

    /**
     * Obtenir les informations de paiement d’un concours.
     *
     * @param string $concoursId ID du concours
     *
     * @return array Tableau contenant toutes les informations de paiement
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
            // Informations bancaires de base
            'montant' => $config->montant,
            'montant_total' => $config->montantTotal(),
            'banque_nom' => $config->banque_nom,
            'numero_compte' => $config->numero_compte,
            'nom_beneficiaire' => $config->nom_beneficiaire,

            // Informations bancaires complètes
            'devise' => $config->devise,
            'code_banque' => $config->code_banque,
            'agence_banque' => $config->agence_banque,
            'iban' => $config->iban,

            // Configuration paiement
            'type_paiement' => $config->type_paiement,
            'banques_acceptees' => $config->banques_acceptees,
            'frais_paiement' => $config->frais_paiement,

            // Date et validation
            'date_limite' => $config->date_limite,
            'reference_format' => $config->reference_format,
            'minimum_confiance_ocr' => $config->minimum_confiance_ocr,
            'validation_auto' => $config->validation_auto,

            // Instructions et métadonnées
            'instructions' => $config->instructions,
            'commentaires' => $config->commentaires,
            'est_actif' => $config->est_actif,
            'est_expire' => $config->isExpire(),
            'jours_restants' => $config->joursRestants(),
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
        $config->update([
            'date_limite' => $newDeadline,
            'date_derniere_modification' => now()
        ]);

        return $config->fresh();
    }

    /**
     * Valider les données de configuration avant sauvegarde.
     *
     * @param ConfigurePaymentDTO $dto DTO de configuration
     *
     * @return array Tableau des erreurs (vide si aucune erreur)
     */
    public function validerConfiguration(ConfigurePaymentDTO $dto): array
    {
        $errors = [];

        // Validation des champs obligatoires
        if (empty($dto->banque_nom)) {
            $errors['banque_nom'] = 'Le nom de la banque est obligatoire';
        }

        if (empty($dto->numero_compte)) {
            $errors['numero_compte'] = 'Le numéro de compte est obligatoire';
        }

        if (empty($dto->nom_beneficiaire)) {
            $errors['nom_beneficiaire'] = 'Le nom du bénéficiaire est obligatoire';
        }

        if ($dto->montant <= 0) {
            $errors['montant'] = 'Le montant doit être supérieur à 0';
        }

        if (empty($dto->date_limite) || strtotime($dto->date_limite) < time()) {
            $errors['date_limite'] = 'La date limite doit être dans le futur';
        }

        // Validation des champs optionnels
        if ($dto->devise && !in_array($dto->devise, ['XAF', 'USD', 'EUR'])) {
            $errors['devise'] = 'La devise doit être XAF, USD ou EUR';
        }

        if ($dto->code_banque && strlen($dto->code_banque) > 11) {
            $errors['code_banque'] = 'Le code de la banque ne peut pas dépasser 11 caractères';
        }

        if ($dto->iban && strlen($dto->iban) > 34) {
            $errors['iban'] = 'L\'IBAN ne peut pas dépasser 34 caractères';
        }

        if ($dto->type_paiement && !in_array($dto->type_paiement, ['virement', 'cheque', 'mobile_money', 'especes', 'carte_bancaire'])) {
            $errors['type_paiement'] = 'Le type de paiement n\'est pas valide';
        }

        if ($dto->banques_acceptees && !is_array($dto->banques_acceptees)) {
            $errors['banques_acceptees'] = 'Les banques acceptées doivent être une liste';
        }

        if ($dto->minimum_confiance_ocr && ($dto->minimum_confiance_ocr < 0 || $dto->minimum_confiance_ocr > 100)) {
            $errors['minimum_confiance_ocr'] = 'La confiance OCR minimale doit être entre 0 et 100';
        }

        return $errors;
    }

    /**
     * Obtenir les configurations expirant bientôt.
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
     * Statistiques des configurations de paiement.
     *
     * @return array Tableau des statistiques
     */
    public function getStatistiques(): array
    {
        $total = ConcoursPaiement::count();
        $actives = ConcoursPaiement::actif()->count();
        $nonExpirees = ConcoursPaiement::nonExpire()->count();
        $expirees = ConcoursPaiement::where('date_limite', '<', now())->count();
        $montantMoyen = ConcoursPaiement::actif()->avg('montant');

        return [
            'total' => $total,
            'actives' => $actives,
            'non_expirees' => $nonExpirees,
            'expirees' => $expirees,
            'montant_moyen' => round($montantMoyen, 2),
            'taux_activite' => $total > 0 ? round(($actives / $total) * 100, 1) : 0,
        ];
    }
}
