<?php

namespace App\Services\Domain\Candidat;

use App\Models\Candidat;
use App\DTOs\Candidats\VerifyPRUDTO;
use App\DTOs\Candidats\UpdateCandidatProfileDTO;
use App\Services\Domain\User\UserService;
use App\Services\Domain\Candidature\CandidatureService;
use App\Services\Domain\Paiement\PaiementService;
use App\Services\Domain\User\TokenService;
use App\Traits\HasAdvancedSearch;
use App\Traits\HasSmartCache;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use Illuminate\Support\Facades\Cache;


class CandidatService
{
    use HasAdvancedSearch, HasSmartCache, HasActivityLogger;

    protected function getModelTags(): array
    {
        return ['candidats', 'lists'];
    }

    public function __construct(
        private readonly TokenService $tokenService,
        private readonly UserService $userService,
        private readonly CandidatureService $candidatureService,
        private readonly PaiementService $paiementService,
        ActivityLoggerService $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Créer un candidat pour le workflow d'inscription
     * 
     * @param string $utilisateurId
     * @param array $eligibilityData ['date_naissance', 'serie_bac', 'nationalite']
     * @param string|null $filiereId
     * @return Candidat
     */
    public function createCandidatForRegistration(string $utilisateurId, array $eligibilityData, ?string $filiereId): Candidat
    {
        $data = [
            'utilisateur_id' => $utilisateurId,
            'date_naissance_cand' => $eligibilityData['date_naissance'],
            'serie_bac' => $eligibilityData['serie_bac'] ?? null,
            'nationalite_cand' => $eligibilityData['nationalite'] ?? 'Camerounaise',
            'sexe_cand' => $eligibilityData['sexe'] ?? null,
            'filiere_id' => $filiereId
        ];

        $candidat = Candidat::create($data);

        $this->logCreate('candidat', $utilisateurId);

        return $candidat;
    }

    /**
     * Vérifier si un numéro CNI est unique
     * 
     * @param string $cni Numéro de CNI à vérifier
     * @param string|null $excludeId ID utilisateur à exclure de la vérification
     * @return bool True si le CNI est unique, false sinon
     */
    public function verifyCNIUnique(string $cni, ?string $excludeId = null): bool
    {
        $query = Candidat::where('numero_cni', $cni);

        if ($excludeId) {
            $query->where('utilisateur_id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    /**
     * Vérifier si un PRU est valide et disponible pour création de compte.
     *
     * @param VerifyPRUDTO $dto DTO contenant le PRU et l'ID du concours
     *
     * @return array Résultat de la validation (valid, message, concours, montant)
     */
    public function verifyPRU(VerifyPRUDTO $dto): array
    {
        return $this->paiementService->isPRUValid($dto->pru, $dto->concoursId);
    }




    /**
     * Mettre à jour le profil candidat.
     *
     * @param string $utilisateurId ID de l'utilisateur lié au candidat
     * @param UpdateCandidatProfileDTO $dto DTO contenant les nouvelles données du profil
     *
     * @return Candidat Candidat mis à jour
     */
    public function updateProfile(string $utilisateurId, UpdateCandidatProfileDTO $dto): Candidat
    {
        return runTransaction(function () use ($utilisateurId, $dto) {
            $candidat = Candidat::where('utilisateur_id', $utilisateurId)->firstOrFail();
            $candidat->update($dto->toArray());
            $this->logUpdate('candidat', $utilisateurId);
            return $candidat->fresh();
        }, 'CandidatService::updateProfile');
    }

    /**
     * Récupérer un candidat par ID utilisateur.
     *
     * @param string $utilisateurId ID de l'utilisateur
     *
     * @return Candidat Candidat avec relations utilisateur, concours et session
     */
    public function getByUserId(string $utilisateurId): Candidat
    {
        $relations = ['utilisateur:id,email,telephone', 'candidatures.concours:id, libelle_concours', 'candidatures.session:id, libelle_session'];
        return Candidat::with($relations)
            ->where('utilisateur_id', $utilisateurId)
            ->firstOrFail();
    }

    /**
     * Récupérer un candidat par PRU.
     *
     * @param string $pru PRU du candidat
     *
     * @return Candidat Candidat avec relations utilisateur et candidatures
     */
    public function getByPRU(string $pru): Candidat
    {
        return Candidat::with(['utilisateur', 'candidatures'])
            ->where('pru', $pru)
            ->firstOrFail();
    }

    /**
     * Liste des candidats avec filtres optimisée (Admin).
     *
     * @param array $filters Filtres disponibles : search, region, est_actif
     * @param int $perPage Nombre d'éléments par page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Liste paginée des candidats
     */
    public function getAll(array $filters = [], int $perPage = 20)
    {
        $query = Candidat::query()
            ->select([
                'utilisateur_id',
                'nom_cand',
                'prenom_cand',
                'date_naissance_cand',
                'lieu_naissance_cand',
                'nationalite_cand',
                'sexe_cand',
                'niveau_scolaire',
                'diplome_admission',
                'mention',
                'region',
                'serie_bac',
                'created_at',
                'updated_at'
            ])
            ->with(['utilisateur:id,user_name,email,telephone,est_actif', 'candidatures:id,code_cand_def']);


        if (!empty($filters['search'])) {
            $this->applySearch(
                $query,
                $filters['search'],
                [
                    'nom_cand' => 'words',
                    'prenom_cand' => 'words',
                    'region' => 'words',
                    'serie_bac' => 'words',
                ],
                [
                    'utilisateur.email' => 'partial',
                    'utilisateur.telephone' => 'start',
                    'utilisateur.user_name' => 'partial'
                ]
            );
        }

        // Filtres simples
        $simpleFilters = [];
        if (isset($filters['region'])) {
            $simpleFilters['region'] = $filters['region'];
        }
        if (isset($filters['serie_bac'])) {
            $simpleFilters['serie_bac'] = $filters['serie_bac'];
        }
        $this->applyFilters($query, $simpleFilters);

        // Filtre sur statut actif via relation
        if (isset($filters['est_actif'])) {
            $query->whereHas('utilisateur', function ($q) use ($filters) {
                $q->where('est_actif', $filters['est_actif']);
            });
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $this->applySort(
            $query,
            $sortBy,
            $sortOrder,
            'created_at',
            ['nom_cand', 'prenom_cand', 'created_at', 'date_naissance']
        );

        return $query->paginate($perPage);
    }

    /**
     * Get all candidats with relations for export
     *
     * @param array $filters
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllForExport(array $filters = [], int $limit = 10000)
    {
        $query = Candidat::query()
            ->with(['utilisateur', 'candidatures.paiement']);

        // Apply same filters as getAll
        if (!empty($filters['search'])) {
            $this->applySearch(
                $query,
                $filters['search'],
                [
                    'nom_cand' => 'words',
                    'prenom_cand' => 'words',
                    'region' => 'words',
                    'serie_bac' => 'words',
                ],
                [
                    'utilisateur.email' => 'partial',
                    'utilisateur.telephone' => 'start',
                    'utilisateur.user_name' => 'partial'
                ]
            );
        }

        $simpleFilters = [];
        if (isset($filters['region'])) {
            $simpleFilters['region'] = $filters['region'];
        }
        if (isset($filters['serie_bac'])) {
            $simpleFilters['serie_bac'] = $filters['serie_bac'];
        }
        $this->applyFilters($query, $simpleFilters);

        if (isset($filters['est_actif'])) {
            $query->whereHas('utilisateur', function ($q) use ($filters) {
                $q->where('est_actif', $filters['est_actif']);
            });
        }

        return $query->latest('created_at')->limit($limit)->get();
    }

    /**
     * Statistiques sur les candidats.
     *
     * @return array 
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
     * Désactiver un candidat.
     *
     * @param string $utilisateurId ID de l'utilisateur lié au candidat
     *
     * @return bool True si désactivation réussie
     */
    public function deactivate(string $utilisateurId): bool
    {
        return $this->userService->deactivate($utilisateurId);
    }

    /**
     * Activer un candidat.
     *
     * @param string $utilisateurId ID de l'utilisateur lié au candidat
     *
     * @return bool True si activation réussie
     */
    public function activate(string $utilisateurId): bool
    {
        return $this->userService->activate($utilisateurId);
    }

    /**
     * Récupérer la session active pour un concours (avec cache).
     *
     * @param mixed $concours Instance du modèle Concours
     *
     * @return mixed Session active ou null
     */
    private function getActiveSessionForConcours($concours)
    {
        $cacheKey = "concours_{$concours->id}_active_session";

        return Cache::remember($cacheKey, 3600, function () use ($concours) {
            return $concours->sessions()
                ->where('statut_session', 'ACTIVE')
                ->first();
        });
    }
}
