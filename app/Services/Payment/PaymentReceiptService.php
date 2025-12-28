<?php

namespace App\Services\Payment;

use App\Models\PaymentReceipt;
use App\Models\Candidat;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentReceiptService
{
    /**
     * Récupérer tous les reçus avec filtres optionnels et pagination
     */
    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = PaymentReceipt::with(['candidat.utilisateur', 'verifiedBy']);

        if (isset($filters['statut_verification'])) {
            $query->where('statut_verification', $filters['statut_verification']);
        }

        if (isset($filters['candidat_id'])) {
            $query->where('candidat_id', $filters['candidat_id']);
        }

        if (isset($filters['date_debut'])) {
            $query->whereDate('date_paiement', '>=', $filters['date_debut']);
        }

        if (isset($filters['date_fin'])) {
            $query->whereDate('date_paiement', '<=', $filters['date_fin']);
        }

        if (isset($filters['banque'])) {
            $query->where('banque', 'like', '%' . $filters['banque'] . '%');
        }

        if (isset($filters['numero_recu'])) {
            $query->where('numero_recu', 'like', '%' . $filters['numero_recu'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Récupérer un reçu par ID
     */
    public function getById(string $id): ?PaymentReceipt
    {
        return PaymentReceipt::with(['candidat.utilisateur', 'verifiedBy'])->find($id);
    }

    /**
     * Récupérer les reçus d'un candidat
     */
    public function getByCandidatId(string $candidatId): Collection
    {
        return PaymentReceipt::where('candidat_id', $candidatId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Récupérer les reçus en attente de vérification avec pagination
     */
    public function getEnAttente(int $perPage = 20): LengthAwarePaginator
    {
        return PaymentReceipt::with(['candidat.utilisateur'])
            ->enAttente()
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    /**
     * Récupérer les reçus vérifiés avec pagination
     */
    public function getVerifies(int $perPage = 20): LengthAwarePaginator
    {
        return PaymentReceipt::with(['candidat.utilisateur', 'verifiedBy'])
            ->verifie()
            ->orderBy('verified_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Récupérer les reçus rejetés avec pagination
     */
    public function getRejetes(int $perPage = 20): LengthAwarePaginator
    {
        return PaymentReceipt::with(['candidat.utilisateur', 'verifiedBy'])
            ->rejete()
            ->orderBy('verified_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Créer un nouveau reçu
     */
    public function create(array $data): PaymentReceipt
    {
        return DB::transaction(function () use ($data) {
            // Vérifier si le numéro de reçu existe déjà
            if (isset($data['numero_recu'])) {
                $exists = PaymentReceipt::where('numero_recu', $data['numero_recu'])->exists();
                if ($exists) {
                    throw new \Exception('Ce numéro de reçu existe déjà');
                }
            }

            return PaymentReceipt::create($data);
        });
    }

    /**
     * Mettre à jour un reçu
     */
    public function update(string $id, array $data): PaymentReceipt
    {
        return DB::transaction(function () use ($id, $data) {
            $receipt = $this->getById($id);
            
            if (!$receipt) {
                throw new \Exception('Reçu non trouvé');
            }

            // Ne pas permettre la modification si déjà vérifié
            if ($receipt->isVerifie() && !isset($data['force_update'])) {
                throw new \Exception('Impossible de modifier un reçu déjà vérifié');
            }

            // Vérifier l'unicité du numéro de reçu si modifié
            if (isset($data['numero_recu']) && $data['numero_recu'] !== $receipt->numero_recu) {
                $exists = PaymentReceipt::where('numero_recu', $data['numero_recu'])
                    ->where('id', '!=', $id)
                    ->exists();
                    
                if ($exists) {
                    throw new \Exception('Ce numéro de reçu existe déjà');
                }
            }

            $receipt->update($data);
            return $receipt->fresh();
        });
    }

    /**
     * Supprimer un reçu
     */
    public function delete(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $receipt = $this->getById($id);
            
            if (!$receipt) {
                throw new \Exception('Reçu non trouvé');
            }

            // Ne pas permettre la suppression si vérifié
            if ($receipt->isVerifie()) {
                throw new \Exception('Impossible de supprimer un reçu vérifié');
            }

            // Supprimer l'image associée
            if ($receipt->image_path && Storage::disk('private')->exists($receipt->image_path)) {
                Storage::disk('private')->delete($receipt->image_path);
            }

            return $receipt->delete();
        });
    }

    /**
     * Vérifier un reçu (approuver)
     */
    public function verifier(string $id, string $userId): PaymentReceipt
    {
        return DB::transaction(function () use ($id, $userId) {
            $receipt = $this->getById($id);
            
            if (!$receipt) {
                throw new \Exception('Reçu non trouvé');
            }

            if ($receipt->isVerifie()) {
                throw new \Exception('Ce reçu a déjà été vérifié');
            }

            $receipt->verifier($userId);
            return $receipt->fresh();
        });
    }

    /**
     * Rejeter un reçu
     */
    public function rejeter(string $id, string $motif, string $userId): PaymentReceipt
    {
        return DB::transaction(function () use ($id, $motif, $userId) {
            $receipt = $this->getById($id);
            
            if (!$receipt) {
                throw new \Exception('Reçu non trouvé');
            }

            if ($receipt->isVerifie()) {
                throw new \Exception('Impossible de rejeter un reçu déjà vérifié');
            }

            if (empty($motif)) {
                throw new \Exception('Le motif de rejet est obligatoire');
            }

            $receipt->rejeter($motif, $userId);
            return $receipt->fresh();
        });
    }

    /**
     * Réinitialiser le statut d'un reçu (remettre en attente)
     */
    public function reinitialiser(string $id): PaymentReceipt
    {
        return DB::transaction(function () use ($id) {
            $receipt = $this->getById($id);
            
            if (!$receipt) {
                throw new \Exception('Reçu non trouvé');
            }

            $receipt->update([
                'statut_verification' => 'en_attente',
                'motif_rejet' => null,
                'verified_at' => null,
                'verified_by' => null,
            ]);

            return $receipt->fresh();
        });
    }

    /**
     * Vérifier si un candidat a un paiement vérifié
     */
    public function candidatHasVerifiedPayment(string $candidatId): bool
    {
        return PaymentReceipt::where('candidat_id', $candidatId)
            ->verifie()
            ->exists();
    }

    /**
     * Obtenir le reçu vérifié d'un candidat
     */
    public function getCandidatVerifiedReceipt(string $candidatId): ?PaymentReceipt
    {
        return PaymentReceipt::where('candidat_id', $candidatId)
            ->verifie()
            ->first();
    }

    /**
     * Obtenir les statistiques des reçus
     */
    public function getStats(): array
    {
        return [
            'total' => PaymentReceipt::count(),
            'en_attente' => PaymentReceipt::enAttente()->count(),
            'verifies' => PaymentReceipt::verifie()->count(),
            'rejetes' => PaymentReceipt::rejete()->count(),
            'montant_total_verifie' => PaymentReceipt::verifie()->sum('montant'),
            'montant_moyen' => PaymentReceipt::verifie()->avg('montant'),
        ];
    }

    /**
     * Obtenir l'URL de l'image du reçu
     */
    public function getImageUrl(PaymentReceipt $receipt): string
    {
        if (!$receipt->image_path) {
            throw new \Exception('Aucune image associée à ce reçu');
        }

        if (!Storage::disk('private')->exists($receipt->image_path)) {
            throw new \Exception('Fichier image introuvable');
        }

        return Storage::disk('private')->url($receipt->image_path);
    }

    /**
     * Obtenir le contenu de l'image du reçu
     */
    public function getImageContent(PaymentReceipt $receipt): string
    {
        if (!$receipt->image_path) {
            throw new \Exception('Aucune image associée à ce reçu');
        }

        if (!Storage::disk('private')->exists($receipt->image_path)) {
            throw new \Exception('Fichier image introuvable');
        }

        return Storage::disk('private')->get($receipt->image_path);
    }

    /**
     * Vérifier si un numéro de reçu existe déjà
     */
    public function numeroRecuExists(string $numeroRecu, ?string $excludeId = null): bool
    {
        $query = PaymentReceipt::where('numero_recu', $numeroRecu);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Rechercher des reçus par numéro avec pagination
     */
    public function searchByNumero(string $numero, int $perPage = 20): LengthAwarePaginator
    {
        return PaymentReceipt::with(['candidat.utilisateur'])
            ->where('numero_recu', 'like', '%' . $numero . '%')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtenir les reçus récents (derniers X jours) avec pagination
     */
    public function getRecents(int $jours = 7, int $perPage = 20): LengthAwarePaginator
    {
        return PaymentReceipt::with(['candidat.utilisateur'])
            ->where('created_at', '>=', now()->subDays($jours))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
