<?php

namespace App\Services\Domain\Concours;

use App\Exceptions\ConcoursException;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\ConcoursFiliere;
use App\Models\Filiere;
use App\Services\Infrastructure\Logger\ActivityLoggerService;
use App\Traits\HasActivityLogger;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConcoursFiliereService
{
    use HasActivityLogger;

    public function __construct(
        private readonly ConcoursService $concoursService,
        ActivityLoggerService $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Valider que la filière appartient à la même école que le concours.
     *
     * @param  Concours  $concours  Concours
     * @param  Filiere  $filiere  Filière à valider
     *
     * @throws ConcoursException Si la filière n'appartient pas à l'école du concours
     */
    private function validateFiliereEcole(Concours $concours, Filiere $filiere): void
    {
        if (! $concours->ecole_id) {
            throw new \Exception('Le concours doit être associé à une école');
        }

        $filiere->load('departement.ecole');
        $filiereEcoleId = $filiere->departement?->ecole_id;

        if ($filiereEcoleId !== $concours->ecole_id) {
            throw ConcoursException::filiereNotFromSameEcole($filiere->libelle_filiere, $concours->libelle_concours);
        }
    }

    /**
     * Valider que le nombre de places est valide.
     *
     * @param  int  $nombrePlaces  Nombre de places à valider
     *
     * @throws ConcoursException Si le nombre de places est <= 0
     */
    private function validateNombrePlaces(int $nombrePlaces): void
    {
        if ($nombrePlaces <= 0) {
            throw ConcoursException::invalidNombrePlaces();
        }
    }

    /**
     * Trouver un concours ou lever une exception.
     *
     * @param  string  $concoursId  ID du concours
     * @return Concours Concours trouvé
     *
     * @throws ConcoursException Si le concours est introuvable
     */
    private function findConcoursOrFail(string $concoursId): Concours
    {
        try {
            return Concours::findOrFail($concoursId);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($concoursId);
        }
    }

    /**
     * Trouver une filière ou lever une exception.
     *
     * @param  string  $filiereId  ID de la filière
     * @return Filiere Filière trouvée
     *
     * @throws ConcoursException Si la filière est introuvable
     */
    private function findFiliereOrFail(string $filiereId): Filiere
    {
        try {
            return Filiere::findOrFail($filiereId);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::filiereNotFound($filiereId);
        }
    }

    /**
     * Vérifier que le concours est attaché à la session.
     *
     * @param  Concours  $concours  Concours à vérifier
     * @param  string  $sessionId  ID de la session
     *
     * @throws ConcoursException Si le concours n'est pas attaché à la session
     */
    private function ensureConcoursAttachedToSession(Concours $concours, string $sessionId): void
    {
        if (! $concours->sessions()->where('sessions.id', $sessionId)->exists()) {
            throw ConcoursException::concoursNotAttachedToSession($concours->id, $sessionId);
        }
    }

    /**
     * Vérifier que la filière est attachée au concours pour la session.
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $filiereId  ID de la filière
     * @return ConcoursFiliere Relation trouvée
     *
     * @throws ConcoursException Si la filière n'est pas attachée
     */
    private function ensureFiliereAttached(string $concoursId, string $sessionId, string $filiereId): ConcoursFiliere
    {
        $relation = $this->getRelation($concoursId, $sessionId, $filiereId);

        if (! $relation) {
            throw ConcoursException::filiereNotAttached($filiereId, $concoursId, $sessionId);
        }

        return $relation;
    }

    /**
     * Vérifier qu'il n'y a pas de candidatures actives pour la filière.
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $filiereId  ID de la filière
     *
     * @throws ConcoursException Si des candidatures actives existent
     */
    private function ensureNoActiveCandidatures(string $concoursId, string $sessionId, string $filiereId): void
    {
        $hasActive = Candidature::where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->whereHas('candidat', function ($query) use ($filiereId) {
                $query->where('filiere_id', $filiereId);
            })
            ->where('statut_candidature', 'VALIDE')
            ->exists();

        if ($hasActive) {
            throw ConcoursException::hasActiveCandidaturesForFiliere($filiereId);
        }
    }

    /**
     * Attacher une filière à un concours pour une session spécifique.
     *
     * Si la filière est déjà attachée, le nombre de places sera mis à jour.
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $filiereId  ID de la filière
     * @param  int  $nombrePlaces  Nombre de places disponibles (doit être > 0)
     * @return ConcoursFiliere Relation créée ou mise à jour
     *
     * @throws ConcoursException Si le concours, la filière est introuvable, le concours n'est pas attaché à la session, ou le nombre de places est invalide
     */
    public function attachFiliere(string $concoursId, string $sessionId, string $filiereId, int $nombrePlaces): ConcoursFiliere
    {
        $concours = $this->findConcoursOrFail($concoursId);
        $filiere = $this->findFiliereOrFail($filiereId);
        $this->ensureConcoursAttachedToSession($concours, $sessionId);
        $this->validateNombrePlaces($nombrePlaces);

        $this->validateFiliereEcole($concours, $filiere);

        return DB::transaction(function () use ($concoursId, $sessionId, $filiereId, $nombrePlaces) {

            $existing = $this->getRelation($concoursId, $sessionId, $filiereId);

            if ($existing) {
                $concours = Concours::findOrFail($concoursId);
                $concours->filieres()->updateExistingPivot($filiereId, [
                    'nombre_places' => $nombrePlaces,
                ], false);

                // Valider la cohérence après mise à jour
                $this->concoursService->validatePlacesCoherence($concoursId, $sessionId);

                return $this->getRelation($concoursId, $sessionId, $filiereId);
            }

            // Créer la relation
            $data = [
                'concours_id' => $concoursId,
                'filiere_id' => $filiereId,
                'nombre_places' => $nombrePlaces,
            ];

            // Ajouter session_id si la colonne existe
            if (Schema::hasColumn('concours_filiere', 'session_id')) {
                $data['session_id'] = $sessionId;
            }

            $result = ConcoursFiliere::create($data);

            // Valider la cohérence après création
            $this->concoursService->validatePlacesCoherence($concoursId, $sessionId);

            return $result;
        });
    }

    /**
     * Détacher une filière d'un concours pour une session.
     *
     * Impossible de détacher si des candidatures actives existent pour cette filière.
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $filiereId  ID de la filière
     * @return bool True si détaché avec succès
     *
     * @throws ConcoursException Si le concours est introuvable, la filière n'est pas attachée, ou s'il y a des candidatures actives
     */
    public function detachFiliere(string $concoursId, string $sessionId, string $filiereId): bool
    {
        $this->findConcoursOrFail($concoursId);
        $this->ensureNoActiveCandidatures($concoursId, $sessionId, $filiereId);

        return DB::transaction(function () use ($concoursId, $sessionId, $filiereId) {
            $relation = $this->ensureFiliereAttached($concoursId, $sessionId, $filiereId);

            return $relation->delete();
        });
    }

    /**
     * Lister toutes les filières d'un concours pour une session.
     *
     * Retourne chaque filière avec ses statistiques :
     * - nombre_places : Nombre total de places
     * - candidatures_validees : Nombre de candidatures validées
     * - places_restantes : Places encore disponibles
     * - taux_remplissage : Pourcentage de remplissage (0-100)
     * - peut_accepter_candidatures : Booléen indiquant si de nouvelles candidatures sont possibles
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @return Collection Collection de tableaux contenant les filières avec leurs statistiques
     *
     * @throws ConcoursException Si le concours est introuvable
     */
    public function listFilieres(string $concoursId, string $sessionId)
    {
        $concours = $this->findConcoursOrFail($concoursId);

        $filieres = $concours->filieresParSession($sessionId)->get();

        return $filieres->map(function ($filiere) use ($concoursId, $sessionId) {
            $pivot = $filiere->pivot;
            $nombreCandidatures = $this->getNombreCandidatures($concoursId, $sessionId, $filiere->id);
            $placesRestantes = max(0, $pivot->nombre_places - $nombreCandidatures);

            return [
                'id' => $filiere->id,
                'code_filiere' => $filiere->code_filiere,
                'libelle_filiere' => $filiere->libelle_filiere,
                'desc_filiere' => $filiere->desc_filiere,
                'nombre_places' => $pivot->nombre_places,
                'candidatures_validees' => $nombreCandidatures,
                'places_restantes' => $placesRestantes,
                'taux_remplissage' => $this->calculerTauxRemplissage($nombreCandidatures, $pivot->nombre_places),
                'peut_accepter_candidatures' => $placesRestantes > 0,
            ];
        });
    }

    /**
     * Obtenir les statistiques détaillées d'une filière pour un concours et une session.
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $filiereId  ID de la filière
     * @return array Tableau contenant les statistiques détaillées
     *
     * @throws ConcoursException Si le concours ou la filière est introuvable, ou si la filière n'est pas attachée
     */
    public function getStats(string $concoursId, string $sessionId, string $filiereId): array
    {
        $this->findConcoursOrFail($concoursId);
        $relation = $this->ensureFiliereAttached($concoursId, $sessionId, $filiereId);
        $filiere = $this->findFiliereOrFail($filiereId);
        $nombreCandidatures = $this->getNombreCandidatures($concoursId, $sessionId, $filiereId);
        $placesRestantes = max(0, $relation->nombre_places - $nombreCandidatures);

        // Statistiques par statut
        $statsParStatut = Candidature::where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->whereHas('candidat', function ($query) use ($filiereId) {
                $query->where('filiere_id', $filiereId);
            })
            ->selectRaw('statut_candidature, COUNT(*) as count')
            ->groupBy('statut_candidature')
            ->pluck('count', 'statut_candidature')
            ->toArray();

        return [
            'filiere' => [
                'id' => $filiere->id,
                'code_filiere' => $filiere->code_filiere,
                'libelle_filiere' => $filiere->libelle_filiere,
                'desc_filiere' => $filiere->desc_filiere,
            ],
            'places' => [
                'total' => $relation->nombre_places,
                'occupees' => $nombreCandidatures,
                'restantes' => $placesRestantes,
                'taux_remplissage' => $this->calculerTauxRemplissage($nombreCandidatures, $relation->nombre_places),
            ],
            'candidatures' => [
                'total' => Candidature::where('concours_id', $concoursId)
                    ->where('session_id', $sessionId)
                    ->whereHas('candidat', function ($query) use ($filiereId) {
                        $query->where('filiere_id', $filiereId);
                    })
                    ->count(),
                'validees' => $nombreCandidatures,
                'par_statut' => $statsParStatut,
            ],
            'peut_accepter_candidatures' => $placesRestantes > 0,
        ];
    }

    /**
     * Mettre à jour le nombre de places d'une filière.
     *
     * Le nouveau nombre de places ne peut pas être inférieur au nombre de candidatures validées.
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $filiereId  ID de la filière
     * @param  int  $nombrePlaces  Nouveau nombre de places (doit être > 0 et >= candidatures validées)
     * @return ConcoursFiliere Relation mise à jour
     *
     * @throws ConcoursException Si le concours est introuvable, la filière n'est pas attachée, le nombre de places est invalide, ou inférieur aux candidatures validées
     */
    public function updateNombrePlaces(string $concoursId, string $sessionId, string $filiereId, int $nombrePlaces): ConcoursFiliere
    {
        $this->validateNombrePlaces($nombrePlaces);
        $relation = $this->ensureFiliereAttached($concoursId, $sessionId, $filiereId);

        $nombreCandidatures = $this->getNombreCandidatures($concoursId, $sessionId, $filiereId);

        if ($nombrePlaces < $nombreCandidatures) {
            throw ConcoursException::placesInferiorToCandidatures($nombreCandidatures);
        }

        return DB::transaction(function () use ($concoursId, $sessionId, $filiereId, $nombrePlaces) {
            // Mettre à jour via la relation
            $concours = Concours::findOrFail($concoursId);
            $concours->filieres()->updateExistingPivot($filiereId, [
                'nombre_places' => $nombrePlaces,
            ], false);

            // Valider la cohérence après mise à jour
            $this->concoursService->validatePlacesCoherence($concoursId, $sessionId);

            return $this->getRelation($concoursId, $sessionId, $filiereId);
        });
    }

    /**
     * Obtenir la relation concours-filiere-session.
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $filiereId  ID de la filière
     * @return ConcoursFiliere|null Relation trouvée ou null
     */
    private function getRelation(string $concoursId, string $sessionId, string $filiereId): ?ConcoursFiliere
    {
        $query = ConcoursFiliere::where('concours_id', $concoursId)
            ->where('filiere_id', $filiereId);

        if (Schema::hasColumn('concours_filiere', 'session_id')) {
            $query->where('session_id', $sessionId);
        }

        return $query->first();
    }

    /**
     * Calculer le taux de remplissage en pourcentage.
     *
     * @param  int  $occupees  Nombre de places occupées
     * @param  int  $total  Nombre total de places
     * @return float Taux de remplissage en pourcentage (0-100)
     */
    private function calculerTauxRemplissage(int $occupees, int $total): float
    {
        return $total > 0 ? round(($occupees / $total) * 100, 2) : 0;
    }

    /**
     * Obtenir le nombre de candidatures validées pour une filière.
     *
     * @param  string  $concoursId  ID du concours
     * @param  string  $sessionId  ID de la session
     * @param  string  $filiereId  ID de la filière
     * @return int Nombre de candidatures avec statut VALIDE
     */
    private function getNombreCandidatures(string $concoursId, string $sessionId, string $filiereId): int
    {
        return Candidature::where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->whereHas('candidat', function ($query) use ($filiereId) {
                $query->where('filiere_id', $filiereId);
            })
            ->where('statut_candidature', 'VALIDE')
            ->count();
    }

    /**
     * Récupérer les filières disponibles (non attachées) pour un concours.
     * Basé sur la session du concours.
     *
     * @param  string  $concoursId  ID du concours
     * @return \Illuminate\Database\Eloquent\Collection Collection des filières disponibles
     *
     * @throws ConcoursException Si le concours n'est attaché à aucune session
     */
    public function getFilieresDisponibles(string $concoursId)
    {
        $concours = $this->findConcoursOrFail($concoursId);

        // Récupérer la session du concours
        $session = $concours->sessions()->first();

        if (! $session) {
            throw ConcoursException::noSessionAttached($concoursId);
        }

        // Récupérer les IDs des filières déjà attachées
        $attachedFiliereIds = $concours->filieres()
            ->wherePivot('session_id', $session->id)
            ->pluck('filieres.id')
            ->toArray();

        // Récupérer toutes les filières actives non attachées DE LA MÊME ÉCOLE
        $query = Filiere::with([
            'departement:id,libelle_departement,code_departement,ecole_id',
            'niveaux:id,libelle_niveau',
        ])
            ->where('est_actif', true)
            ->whereNotIn('id', $attachedFiliereIds);

        // Filtrer par école si le concours a une école associée
        if ($concours->ecole_id) {
            $query->whereHas('departement', function ($q) use ($concours) {
                $q->where('ecole_id', $concours->ecole_id);
            });
        }

        return $query->orderBy('libelle_filiere')->get();
    }
}
