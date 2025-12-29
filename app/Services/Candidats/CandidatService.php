<?php

namespace App\Services\Candidats;

use App\Models\Candidat;
use App\Models\Utilisateur;
use App\Models\Paiement;
use App\Models\Candidature;
use App\DTOs\Candidats\VerifyPRUDTO;
use App\DTOs\Candidats\RegisterCandidatDTO;
use App\DTOs\Candidats\LoginCandidatDTO;
use App\DTOs\Candidats\UpdateCandidatProfileDTO;
use App\Enums\TypeUtilisateur;
use App\Enums\StatutPaiement;
use App\Enums\StatutInscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CandidatService
{
    /**
     * Vérifier si un PRU est valide et disponible pour création de compte
     */
    public function verifyPRU(VerifyPRUDTO $dto): array
    {
        $paiement = Paiement::where('reference', $dto->pru)
            ->where('concours_id', $dto->concoursId)
            ->where('statut', StatutPaiement::VERIFIED)
            ->whereNull('candidat_id')
            ->first();

        if (!$paiement) {
            return [
                'valid' => false,
                'message' => 'PRU invalide ou déjà utilisé'
            ];
        }

        // Vérifier date limite
        $config = $paiement->concours->configurationPaiement;
        if ($config && $config->date_limite < now()) {
            return [
                'valid' => false,
                'message' => 'La date limite d\'inscription est dépassée'
            ];
        }

        return [
            'valid' => true,
            'concours' => $paiement->concours,
            'montant' => $paiement->montant
        ];
    }

    /**
     * Créer un compte candidat après validation du paiement
     * WORKFLOW: PRU validé → Création Utilisateur → Création Candidat → Liaison Paiement → Création Candidature
     */
    public function register(RegisterCandidatDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            // 1. Vérifier PRU
            $verifyDTO = new VerifyPRUDTO($dto->pru, $dto->concoursId);
            $verification = $this->verifyPRU($verifyDTO);
            
            if (!$verification['valid']) {
                throw new \Exception($verification['message']);
            }

            // 2. Vérifier que l'email n'existe pas
            if (Utilisateur::where('email', $dto->email)->exists()) {
                throw new \Exception('Cet email est déjà utilisé');
            }

            // 3. Créer Utilisateur (username = PRU)
            $utilisateur = Utilisateur::create([
                'user_name' => $dto->pru,
                'email' => $dto->email,
                'mot_de_passe' => Hash::make($dto->password),
                'telephone' => $dto->telephone,
                'type_utilisateur' => TypeUtilisateur::CANDIDAT,
                'est_actif' => true,
                'email_verifie' => false,
            ]);

            // 4. Créer Candidat
            $candidat = Candidat::create([
                'utilisateur_id' => $utilisateur->id,
                'nom_cand' => $dto->nom,
                'prenom_cand' => $dto->prenom,
                'pru' => $dto->pru,
                'telephone_candidat' => $dto->telephone,
                'nationalite_cand' => 'Camerounaise',
            ]);

            // 5. Lier Paiement au Candidat
            $paiement = Paiement::where('reference', $dto->pru)
                ->where('concours_id', $dto->concoursId)
                ->firstOrFail();
            
            $paiement->update(['candidat_id' => $utilisateur->id]);

            // 6. Créer Candidature automatiquement avec statut ACTIF
            $candidature = Candidature::create([
                'candidat_id' => $utilisateur->id,
                'concours_id' => $dto->concoursId,
                'session_id' => $dto->sessionId,
                'statut_inscription' => StatutInscription::ACTIF,
                'date_candidature' => now(),
                'date_inscription' => $paiement->validated_at ?? now(),
            ]);

            // 7. Générer token
            $token = $utilisateur->createToken('auth_token')->plainTextToken;

            return [
                'user' => $utilisateur->load('candidat'),
                'candidature' => $candidature,
                'token' => $token,
            ];
        });
    }

    /**
     * Login avec PRU + password
     */
    public function login(LoginCandidatDTO $dto): array
    {
        $utilisateur = Utilisateur::where('user_name', $dto->pru)
            ->where('type_utilisateur', TypeUtilisateur::CANDIDAT)
            ->where('est_actif', true)
            ->first();

        if (!$utilisateur || !Hash::check($dto->password, $utilisateur->mot_de_passe)) {
            throw new \Exception('PRU ou mot de passe incorrect');
        }

        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        return [
            'user' => $utilisateur->load(['candidat', 'candidat.candidatures']),
            'token' => $token,
        ];
    }

    /**
     * Mettre à jour le profil candidat
     */
    public function updateProfile(string $utilisateurId, UpdateCandidatProfileDTO $dto): Candidat
    {
        return DB::transaction(function () use ($utilisateurId, $dto) {
            $candidat = Candidat::where('utilisateur_id', $utilisateurId)->firstOrFail();
            $candidat->update($dto->toArray());
            return $candidat->fresh();
        });
    }

    /**
     * Récupérer un candidat par ID
     */
    public function getById(string $utilisateurId): Candidat
    {
        $candidat = Candidat::with(['utilisateur', 'candidatures.concours', 'candidatures.session'])
            ->where('utilisateur_id', $utilisateurId)
            ->firstOrFail();

        return $candidat;
    }

    /**
     * Récupérer un candidat par PRU
     */
    public function getByPRU(string $pru): Candidat
    {
        $candidat = Candidat::with(['utilisateur', 'candidatures'])
            ->where('pru', $pru)
            ->firstOrFail();

        return $candidat;
    }

    /**
     * Liste des candidats (Admin)
     */
    public function getAll(array $filters = [], int $perPage = 20)
    {
        $query = Candidat::with(['utilisateur']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nom_cand', 'like', "%{$search}%")
                  ->orWhere('prenom_cand', 'like', "%{$search}%")
                  ->orWhere('pru', 'like', "%{$search}%")
                  ->orWhere('telephone_candidat', 'like', "%{$search}%");
            });
        }

        if (isset($filters['region'])) {
            $query->where('region', $filters['region']);
        }

        if (isset($filters['est_actif'])) {
            $query->whereHas('utilisateur', function ($q) use ($filters) {
                $q->where('est_actif', $filters['est_actif']);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Statistiques candidats
     */
    public function getStats(): array
    {
        $total = Candidat::count();
        $actifs = Candidat::whereHas('utilisateur', fn($q) => $q->where('est_actif', true))->count();

        return [
            'total' => $total,
            'actifs' => $actifs,
            'inactifs' => $total - $actifs,
            'inscrits_aujourdhui' => Candidat::whereDate('created_at', today())->count(),
            'inscrits_ce_mois' => Candidat::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'par_region' => Candidat::selectRaw('region, COUNT(*) as count')
                ->whereNotNull('region')
                ->groupBy('region')
                ->orderByDesc('count')
                ->pluck('count', 'region')
                ->toArray(),
        ];
    }

    /**
     * Désactiver un candidat
     */
    public function deactivate(string $utilisateurId): bool
    {
        return DB::transaction(function () use ($utilisateurId) {
            $utilisateur = Utilisateur::findOrFail($utilisateurId);
            $utilisateur->update(['est_actif' => false]);
            $utilisateur->tokens()->delete();
            return true;
        });
    }

    /**
     * Activer un candidat
     */
    public function activate(string $utilisateurId): bool
    {
        $utilisateur = Utilisateur::findOrFail($utilisateurId);
        $utilisateur->update(['est_actif' => true]);
        return true;
    }
}
