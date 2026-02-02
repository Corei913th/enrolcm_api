<?php

namespace App\Services\Domain\Paiement;

use App\Models\ConcoursPaiement;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class ConcoursPaiementService
{
    use HasActivityLogger;

    public function __construct(ActivityLoggerService $logger)
    {
        $this->logger = $logger;
    }
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
        return runTransaction(function () use ($concoursId, $data) {
            $config = ConcoursPaiement::where('concours_id', $concoursId)->first();

            if ($config) {
                $config->update($data);
                $this->logUpdate('concours_paiement', $config->id);
                return $config->fresh();
            }

            $newConfig = ConcoursPaiement::create(array_merge($data, [
                'concours_id' => $concoursId,
            ]));
            $this->logCreate('concours_paiement', $newConfig->id, ['concours_id' => $concoursId]);
            return $newConfig;
        }, 'ConcoursPaiementService::configurerPaiement');
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
        $this->logStatusChange('concours_paiement', $configId, false);
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
        $this->logStatusChange('concours_paiement', $configId, true);
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
     * Vérifier si une banque est acceptée pour cette configuration.
     *
     * @param ConcoursPaiement $config Configuration de paiement
     * @param string $nomBanque Nom de la banque à vérifier
     *
     * @return bool True si la banque est acceptée
     */
    public function banqueEstAcceptee(ConcoursPaiement $config, string $nomBanque): bool
    {
        return $config->banqueEstAcceptee($nomBanque);
    }

    /**
     * Vérifier si la validation automatique est possible pour cette configuration.
     *
     * @param ConcoursPaiement $config Configuration de paiement
     *
     * @return bool True si la validation auto est possible
     */
    public function peutValiderAutomatiquement(ConcoursPaiement $config): bool
    {
        return $config->peutValiderAutomatiquement();
    }

    /**
     * Récupérer les informations bancaires complètes d'une configuration.
     *
     * @param ConcoursPaiement $config Configuration de paiement
     *
     * @return array Informations bancaires formatées
     */
    public function getInformationsBancaires(ConcoursPaiement $config): array
    {
        return $config->getInformationsBancaires();
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

        // Validation des champs obligatoires de base
        if (!isset($data['banque_nom']) || empty($data['banque_nom'])) {
            $errors['banque_nom'] = 'Le nom de la banque est obligatoire';
        }

        if (!isset($data['numero_compte']) || empty($data['numero_compte'])) {
            $errors['numero_compte'] = 'Le numéro de compte est obligatoire';
        }

        if (!isset($data['nom_beneficiaire']) || empty($data['nom_beneficiaire'])) {
            $errors['nom_beneficiaire'] = 'Le nom du bénéficiaire est obligatoire';
        }

        if (!isset($data['montant']) || $data['montant'] <= 0) {
            $errors['montant'] = 'Le montant doit être supérieur à 0';
        }

        if (!isset($data['date_limite']) || strtotime($data['date_limite']) < time()) {
            $errors['date_limite'] = 'La date limite doit être dans le futur';
        }

        // Validation des champs optionnels
        if (isset($data['devise']) && !in_array($data['devise'], ['XAF', 'USD', 'EUR'])) {
            $errors['devise'] = 'La devise doit être XAF, USD ou EUR';
        }

        if (isset($data['code_banque']) && strlen($data['code_banque']) > 11) {
            $errors['code_banque'] = 'Le code de la banque ne peut pas dépasser 11 caractères';
        }

        if (isset($data['iban']) && strlen($data['iban']) > 34) {
            $errors['iban'] = 'L\'IBAN ne peut pas dépasser 34 caractères';
        }

        if (isset($data['type_paiement']) && !in_array($data['type_paiement'], ['virement', 'cheque', 'mobile_money', 'especes', 'carte_bancaire'])) {
            $errors['type_paiement'] = 'Le type de paiement n\'est pas valide';
        }

        if (isset($data['banques_acceptees']) && !is_array($data['banques_acceptees'])) {
            $errors['banques_acceptees'] = 'Les banques acceptées doivent être une liste';
        }

        if (isset($data['minimum_confiance_ocr']) && ($data['minimum_confiance_ocr'] < 0 || $data['minimum_confiance_ocr'] > 100)) {
            $errors['minimum_confiance_ocr'] = 'La confiance OCR minimale doit être entre 0 et 100';
        }

        return $errors;
    }
}
