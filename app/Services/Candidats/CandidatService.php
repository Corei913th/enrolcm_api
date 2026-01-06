<?php

namespace App\Services\Candidats;

use App\Models\Candidat;
use App\DTOs\Candidats\VerifyPRUDTO;
use App\DTOs\Candidats\RegisterCandidatDTO;
use App\DTOs\Candidats\LoginCandidatDTO;
use App\DTOs\Candidats\UpdateCandidatProfileDTO;
use App\Services\Users\UserService;
use App\Services\Candidature\CandidatureService;
use App\Services\Payment\PaiementService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CandidatService
{
    public function __construct(
        private readonly UserService $userService,
        private readonly CandidatureService $candidatureService,
        private readonly PaiementService $paiementService
    ) {}

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
     * Créer un compte candidat après validation du paiement.
     *
     * Workflow : PRU validé → Création Utilisateur → Création Candidat → Liaison Paiement → Création Candidature.
     *
     * @param RegisterCandidatDTO $dto DTO contenant les informations du candidat
     *
     * @return array Tableau contenant :
     *
     * @throws \Exception Si PRU invalide ou email déjà utilisé
     */
    public function register(RegisterCandidatDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {

            $paiementInfo = $this->paiementService->getPaiementInfo($dto->pru);

            if (!$paiementInfo) {
                throw new \Exception('PRU invalide ou déjà utilisé');
            }

            $concoursId = $paiementInfo['concours_id'];


            if (isset($dto->concoursId) && $dto->concoursId !== $concoursId) {
                throw new \Exception('Le PRU ne correspond pas au concours sélectionné');
            }


            if ($this->userService->emailExists($dto->email)) {
                throw new \Exception('Cet email est déjà utilisé');
            }

            $utilisateur = $this->userService->createCandidatUser(
                pru: $dto->pru,
                email: $dto->email,
                password: $dto->password,
                telephone: $dto->telephone
            );

            $candidat = Candidat::create([
                'utilisateur_id' => $utilisateur->id,
                'nom_cand' => $dto->nom,
                'prenom_cand' => $dto->prenom,
                'nationalite_cand' => 'Camerounaise',
            ]);

            $this->paiementService->linkToCandidat($dto->pru, $concoursId, $candidat->utilisateur_id);
            $dateInscription = $paiementInfo['validated_at'];


            $sessionActive = $this->getActiveSessionForConcours($paiementInfo['concours']);

            if (!$sessionActive) {
                throw new \Exception('Aucune session active trouvée pour ce concours');
            }

            $candidature = $this->candidatureService->createCandidature(
                $candidat,
                $concoursId,
                $sessionActive,
                $dateInscription
            );


            return [
                'user' => $utilisateur->load('candidat'),
                'candidature' => $candidature,
            ];
        });
    }

    /**
     * Authentifier un candidat avec PRU + mot de passe.
     *
     * @param LoginCandidatDTO $dto DTO contenant PRU et mot de passe
     *
     * @return array 
     *
     * @throws \Exception Si les identifiants sont incorrects
     */
    public function login(LoginCandidatDTO $dto): array
    {
        $utilisateur = $this->userService->authenticateCandidat($dto->pru, $dto->password);

        if (!$utilisateur) {
            throw new \Exception('PRU ou mot de passe incorrect');
        }

        $token = $this->userService->generateToken($utilisateur);

        return [
            'user' => $utilisateur->load(['candidat', 'candidat.candidatures']),
            'token' => $token,
        ];
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
        return DB::transaction(function () use ($utilisateurId, $dto) {
            $candidat = Candidat::where('utilisateur_id', $utilisateurId)->firstOrFail();
            $candidat->update($dto->toArray());
            return $candidat->fresh();
        });
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
        $relations = ['utilisateur:id,email, telephone', 'candidatures.concours:id, libelle_concours', 'candidatures.session:id, libelle_session'];
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
     * Liste des candidats avec filtres (Admin).
     *
     * @param array $filters Filtres disponibles : search, region, est_actif
     * @param int $perPage Nombre d'éléments par page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Liste paginée des candidats
     */
    public function getAll(array $filters = [], int $perPage = 20)
    {
        $query = Candidat::with(['utilisateur']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nom_cand', 'like', "%{$search}%")
                    ->orWhere('prenom_cand', 'like', "%{$search}%");
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
     * Statistiques sur les candidats.
     *
     * @return array Tableau contenant :
     *   - total : nombre total de candidats
     *   - actifs : nombre de candidats actifs
     *   - inactifs : nombre de candidats inactifs
     *   - inscrits_aujourdhui : nombre de candidats inscrits aujourd'hui
     *   - inscrits_ce_mois : nombre de candidats inscrits ce mois
     *   - par_region : répartition des candidats par région
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
