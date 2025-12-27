<?php

namespace App\Services\Candidats;

use App\DTOs\Auth\CreateCandidatAccountDTO;
use App\DTOs\Candidats\UpdateCandidatDTO;
use App\Exceptions\Business\ResourceNotFoundException;
use App\Models\Candidat;
use App\Models\Utilisateur;
use App\Services\Roles\RoleService;
use App\Services\Users\UserService;
use Exception;
use Illuminate\Support\Facades\DB;

class  CandidatService
{

    public function __construct(
        private readonly UserService $users,
        private readonly RoleService $roles,
    ) {}


    public function createPartialCandidat(
        CreateCandidatAccountDTO $dto,
        ?Utilisateur $user = null
    ): Utilisateur {
        return DB::transaction(function () use ($dto, $user) {

            $existingCandidat = Candidat::where('numero_recu', $dto->user_name)->first();
            if ($existingCandidat) {
                throw new Exception(
                    'Un candidat avec ce numéro de reçu existe déjà.'
                );
            }

            $user ??= $this->users->createCandidatAccount($dto);

            $candidat = Candidat::create([
                'utilisateur_id'     => $user->id,
                'numero_recu'        => $dto->user_name,
                'nationalite_cand'   => $dto->nationalite_cand,
            ]);

            $this->roles->assignDefault($user, 'CANDIDAT');

            return $user->setRelation('candidat', $candidat);
        });
    }

    public function updateCandidat(
        UpdateCandidatDTO $dto,
    ): Candidat {
        return DB::transaction(function () use ($dto) {

            $existingCandidat = Candidat::where('utilisateur_id', $dto->utilisateur_id)->first();
            if (!$existingCandidat) {
                throw new ResourceNotFoundException(
                    'Candidat',
                    $dto->utilisateur_id
                );
            }

            $existingCandidat->update($dto->toArray());

            return $existingCandidat;
        });
    }

    /**
     * Récupérer tous les candidats avec pagination
     */
    public function getAllCandidats(int $perPage = 15, array $filters = [])
    {
        $query = Candidat::with(['utilisateur']);

        // Par défaut, exclure les candidats inactifs (sauf si demandé explicitement)
        if (!isset($filters['include_inactive']) || !$filters['include_inactive']) {
            $query->whereHas('utilisateur', function ($q) {
                $q->where('est_actif', true);
            });
        }

        // Filtrer uniquement les inactifs si demandé
        if (isset($filters['only_inactive']) && $filters['only_inactive']) {
            $query->whereHas('utilisateur', function ($q) {
                $q->where('est_actif', false);
            });
        }

        // Filtres optionnels
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nom_cand', 'like', "%{$search}%")
                  ->orWhere('prenom_cand', 'like', "%{$search}%")
                  ->orWhere('numero_recu', 'like', "%{$search}%")
                  ->orWhere('telephone_candidat', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['region'])) {
            $query->where('region', $filters['region']);
        }

        if (!empty($filters['sexe_cand'])) {
            $query->where('sexe_cand', $filters['sexe_cand']);
        }

        if (!empty($filters['nationalite_cand'])) {
            $query->where('nationalite_cand', $filters['nationalite_cand']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Récupérer un candidat par son ID utilisateur
     */
    public function getCandidatById(string $utilisateurId): Candidat
    {
        $candidat = Candidat::with(['utilisateur'])->where('utilisateur_id', $utilisateurId)->first();

        if (!$candidat) {
            throw new ResourceNotFoundException('Candidat', $utilisateurId);
        }

        return $candidat;
    }

    /**
     * Récupérer un candidat par son numéro de reçu
     */
    public function getCandidatByNumeroRecu(string $numeroRecu): Candidat
    {
        $candidat = Candidat::with(['utilisateur'])->where('numero_recu', $numeroRecu)->first();

        if (!$candidat) {
            throw new ResourceNotFoundException('Candidat', $numeroRecu);
        }

        return $candidat;
    }

    /**
     * Supprimer un candidat (soft delete - désactivation du compte)
     */
    public function deleteCandidat(string $utilisateurId): bool
    {
        return DB::transaction(function () use ($utilisateurId) {
            $candidat = $this->getCandidatById($utilisateurId);

            // Désactiver l'utilisateur associé (soft delete)
            $utilisateur = Utilisateur::find($utilisateurId);
            if ($utilisateur) {
                $utilisateur->update(['est_actif' => false]);
                
                // Révoquer tous les tokens d'accès
                $utilisateur->tokens()->delete();
                
                return true;
            }

            return false;
        });
    }

    /**
     * Réactiver un candidat
     */
    public function activateCandidat(string $utilisateurId): bool
    {
        $candidat = $this->getCandidatById($utilisateurId);

        $utilisateur = Utilisateur::find($utilisateurId);
        if ($utilisateur) {
            $utilisateur->update(['est_actif' => true]);
            return true;
        }

        return false;
    }

    /**
     * Rechercher des candidats selon des critères
     */
    public function searchCandidats(array $criteria, int $perPage = 15)
    {
        $query = Candidat::with(['utilisateur']);

        foreach ($criteria as $field => $value) {
            if (!empty($value)) {
                if (in_array($field, ['nom_cand', 'prenom_cand', 'numero_recu'])) {
                    $query->where($field, 'like', "%{$value}%");
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Obtenir les statistiques des candidats
     */
    public function getCandidatStats(): array
    {
        $total = Candidat::count();
        $actifs = Candidat::whereHas('utilisateur', fn($q) => $q->where('est_actif', true))->count();
        
        return [
            // Statistiques générales
            'total' => $total,
            'actifs' => $actifs,
            'inactifs' => $total - $actifs,
            'inscrits_recemment' => Candidat::where('created_at', '>=', now()->subDays(7))->count(),
            'inscrits_aujourdhui' => Candidat::whereDate('created_at', today())->count(),
            'inscrits_ce_mois' => Candidat::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            
            // Répartition par sexe
            'par_sexe' => Candidat::selectRaw('sexe_cand, COUNT(*) as count')
                ->whereNotNull('sexe_cand')
                ->groupBy('sexe_cand')
                ->pluck('count', 'sexe_cand')
                ->toArray(),
            
            // Répartition par région (important pour concours nationaux)
            'par_region' => Candidat::selectRaw('region, COUNT(*) as count')
                ->whereNotNull('region')
                ->groupBy('region')
                ->orderByDesc('count')
                ->pluck('count', 'region')
                ->toArray(),
            
            // Répartition par nationalité
            'par_nationalite' => Candidat::selectRaw('nationalite_cand, COUNT(*) as count')
                ->whereNotNull('nationalite_cand')
                ->groupBy('nationalite_cand')
                ->orderByDesc('count')
                ->pluck('count', 'nationalite_cand')
                ->toArray(),
            
            // Répartition par tranche d'âge
            'par_tranche_age' => [
                '16-20' => Candidat::whereBetween('age_cand', [16, 20])->count(),
                '21-25' => Candidat::whereBetween('age_cand', [21, 25])->count(),
                '26-30' => Candidat::whereBetween('age_cand', [26, 30])->count(),
                '31-35' => Candidat::whereBetween('age_cand', [31, 35])->count(),
            ],
            
            // Statistiques académiques
            'avec_diplome' => Candidat::whereNotNull('diplome_admission')->count(),
            'par_niveau_scolaire' => Candidat::selectRaw('niveau_scolaire, COUNT(*) as count')
                ->whereNotNull('niveau_scolaire')
                ->groupBy('niveau_scolaire')
                ->orderByDesc('count')
                ->pluck('count', 'niveau_scolaire')
                ->toArray(),
            'par_filiere_origine' => Candidat::selectRaw('filiere_origine, COUNT(*) as count')
                ->whereNotNull('filiere_origine')
                ->groupBy('filiere_origine')
                ->orderByDesc('count')
                ->limit(10) // Top 10 filières
                ->pluck('count', 'filiere_origine')
                ->toArray(),
            'par_mention' => Candidat::selectRaw('mention, COUNT(*) as count')
                ->whereNotNull('mention')
                ->groupBy('mention')
                ->orderByDesc('count')
                ->pluck('count', 'mention')
                ->toArray(),
            
            // Statistiques sociales
            'avec_handicap' => Candidat::whereNotNull('handicap')->count(),
            'par_statut_matrimonial' => Candidat::selectRaw('statut_matrimonial, COUNT(*) as count')
                ->whereNotNull('statut_matrimonial')
                ->groupBy('statut_matrimonial')
                ->pluck('count', 'statut_matrimonial')
                ->toArray(),
            
            // Taux de complétion du profil
            'profils_complets' => Candidat::whereNotNull('nom_cand')
                ->whereNotNull('prenom_cand')
                ->whereNotNull('date_naissance_cand')
                ->whereNotNull('numero_cni')
                ->whereNotNull('telephone_candidat')
                ->count(),
            'profils_incomplets' => Candidat::where(function($q) {
                $q->whereNull('nom_cand')
                  ->orWhereNull('prenom_cand')
                  ->orWhereNull('date_naissance_cand')
                  ->orWhereNull('numero_cni')
                  ->orWhereNull('telephone_candidat');
            })->count(),
            
            // Évolution temporelle (7 derniers jours)
            'evolution_7_jours' => Candidat::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray(),
        ];
    }

    /**
     * Vérifier si un candidat existe
     */
    public function candidatExists(string $utilisateurId): bool
    {
        return Candidat::where('utilisateur_id', $utilisateurId)->exists();
    }

    /**
     * Vérifier si un numéro de reçu existe
     */
    public function numeroRecuExists(string $numeroRecu): bool
    {
        return Candidat::where('numero_recu', $numeroRecu)->exists();
    }
}

