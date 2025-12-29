<?php

namespace App\Services\Concours;

use App\Models\ConcoursPaiement;
use App\Models\Concours;
use App\DTOs\Concours\ConfigurePaymentDTO;
use App\Exceptions\ConcoursException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class ConcoursPaiementService
{
    public function configurePayment(string $concoursId, ConfigurePaymentDTO $dto): ConcoursPaiement
    {
        $concours = Concours::find($concoursId);
        
        if (!$concours) {
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

    public function getConfiguration(string $concoursId): ConcoursPaiement
    {
        $config = ConcoursPaiement::where('concours_id', $concoursId)->first();
        
        if (!$config) {
            throw ConcoursException::paiementNotConfigured($concoursId);
        }

        return $config;
    }

    public function getActiveConfigurations(): Collection
    {
        return ConcoursPaiement::with('concours')
            ->actif()
            ->nonExpire()
            ->get();
    }

    public function deactivate(string $configId): ConcoursPaiement
    {
        $config = ConcoursPaiement::find($configId);
        
        if (!$config) {
            throw new \Exception("Payment configuration not found.", 404);
        }

        $config->update(['est_actif' => false]);
        return $config->fresh();
    }

    public function activate(string $configId): ConcoursPaiement
    {
        $config = ConcoursPaiement::find($configId);
        
        if (!$config) {
            throw new \Exception("Payment configuration not found.", 404);
        }

        $config->update(['est_actif' => true]);
        return $config->fresh();
    }

    public function hasValidConfiguration(string $concoursId): bool
    {
        try {
            $config = $this->getConfiguration($concoursId);
            return $config->est_actif && !$config->isExpire();
        } catch (ConcoursException $e) {
            return false;
        }
    }

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

    public function extendDeadline(string $configId, int $days): ConcoursPaiement
    {
        $config = ConcoursPaiement::find($configId);
        
        if (!$config) {
            throw new \Exception("Payment configuration not found.", 404);
        }
        
        $newDeadline = $config->date_limite->addDays($days);
        $config->update(['date_limite' => $newDeadline]);
        
        return $config->fresh();
    }
}
