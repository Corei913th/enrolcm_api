<?php

namespace App\Services\Concours;

use App\Models\Concours;
use App\Models\EtatSession;
use App\Models\EtatConcoursSession;
use App\DTOs\Concours\CreateConcoursDTO;
use App\DTOs\Concours\UpdateConcoursDTO;
use App\Exceptions\ConcoursException;
use App\Enums\EtatSession as EtatSessionEnum;
use App\Models\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ConcoursService
{
    /**
     * Créer un concours.
     *
     * @param CreateConcoursDTO $dto DTO contenant les informations du concours
     *
     * @return Concours Concours créé avec ses relations
     *
     * @throws ConcoursException Si les dates sont incohérentes
     * @throws \Exception Si le nombre de places est invalide ou si la session est inactive
     */
    public function create(CreateConcoursDTO $dto): Concours
    {
        // VALIDATIONS CONDITIONNELLES : Seulement si session fournie
        if (isset($dto->session_id)) {
            // Dates cohérentes
            if ($dto->date_limite_depot <= $dto->date_debut) {
                throw ConcoursException::invalidDateRange();
            }

            // Nombre de places positif
            if (isset($dto->nombre_places) && $dto->nombre_places <= 0) {
                throw new \Exception('Le nombre de places doit être positif');
            }

            // Session active
            $session = Session::findOrFail($dto->session_id);
            if (!$session->est_actif) {
                throw new \Exception('Impossible de créer un concours pour une session inactive');
            }
            if (!$session->accepteInscriptions()) {
                throw new \Exception('La session n\'accepte plus les inscriptions');
            }

            // Unicité libellé par session
            $existingConcours = Concours::where('libelle_concours', $dto->libelle_concours)
                ->whereHas('sessions', function ($query) use ($dto) {
                    $query->where('sessions.id', $dto->session_id);
                });

            if ($existingConcours->exists()) {
                throw new \Exception('Un concours avec ce libellé existe déjà pour cette session');
            }
        } else {
            // Unicité libellé pour concours templates (sans session)
            $existingConcours = Concours::where('libelle_concours', $dto->libelle_concours)
                ->whereDoesntHave('sessions'); // Seulement les templates

            if ($existingConcours->exists()) {
                throw new \Exception('Un concours template avec ce libellé existe déjà');
            }
        }

        return DB::transaction(function () use ($dto) {
            $concours = Concours::create($dto->toArray());

            if (isset($dto->session_id)) {
                $concours->sessions()->attach($dto->session_id);

                $etatOuverte = EtatSession::getByLibelle(EtatSessionEnum::OUVERTE);

                if ($etatOuverte) {
                    EtatConcoursSession::create([
                        'concours_session_concours_id' => $concours->id,
                        'concours_session_session_id' => $dto->session_id,
                        'etat_session_id' => $etatOuverte->id,
                        'date_etat' => now(),
                    ]);
                }
            }

            return $concours->fresh(['sessions', 'configurationPaiement']);
        });
    }

    /**
     * Mettre à jour un concours existant.
     *
     * @param string $id ID du concours
     * @param UpdateConcoursDTO $dto DTO contenant les nouvelles données
     *
     * @return Concours Concours mis à jour
     *
     * @throws ConcoursException Si le concours est introuvable ou si les dates sont incohérentes
     */
    public function update(string $id, UpdateConcoursDTO $dto): Concours
    {
        try {
            $concours = Concours::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($id);
        }

        $data = $dto->toArray();

        if (isset($data['date_limite_depot'], $data['date_debut']) && $data['date_limite_depot'] <= $data['date_debut']) {
            throw ConcoursException::invalidDateRange();
        }

        return DB::transaction(function () use ($concours, $data) {
            $concours->update($data);
            return $concours->fresh();
        });
    }

    /**
     * Supprimer un concours.
     *
     * @param string $id ID du concours
     *
     * @return bool True si suppression réussie
     *
     * @throws ConcoursException Si le concours est introuvable ou possède des inscriptions actives
     */
    public function delete(string $id): bool
    {
        $concours = Concours::findOrFail($id);

        if ($concours->candidatures()->where('statut_inscription', 'ACTIF')->exists()) {
            throw ConcoursException::hasActiveInscriptions($id);
        }

        return DB::transaction(function () use ($concours) {
            return $concours->delete();
        });
    }

    /**
     * Récupérer un concours par ID.
     *
     * @param string $id ID du concours
     *
     * @return Concours Concours avec ses relations
     *
     * @throws ConcoursException Si le concours est introuvable
     */
    public function getById(string $id): Concours
    {
        try {
            return Concours::with(['specConcours', 'filieres', 'configurationPaiement', 'sessions'])->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($id);
        }
    }

    /**
     * Récupérer tous les concours avec filtres et pagination.
     *
     * @param array $filters Filtres disponibles : est_actif, spec_concours_id, search, session_id
     * @param int $perPage Nombre d’éléments par page
     *
     * @return LengthAwarePaginator Liste paginée des concours
     */
    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Concours::with(['specConcours', 'configurationPaiement', 'sessions']);

        if (isset($filters['est_actif'])) {
            $query->where('est_actif', $filters['est_actif']);
        }

        if (isset($filters['spec_concours_id'])) {
            $query->where('spec_concours_id', $filters['spec_concours_id']);
        }

        if (isset($filters['search'])) {
            $query->where('libelle_concours', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['session_id'])) {
            $query->whereHas('sessions', function ($q) use ($filters) {
                $q->where('sessions.id', $filters['session_id']);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Récupérer les concours disponibles (actifs et non expirés).
     *
     * @param int $perPage Nombre d’éléments par page
     *
     * @return LengthAwarePaginator Liste paginée des concours disponibles
     */
    public function getAvailableConcours(int $perPage = 20): LengthAwarePaginator
    {
        return Concours::with(['configurationPaiement', 'filieres', 'sessions'])
            ->where('est_actif', true)
            ->whereDate('date_limite_depot', '>=', now())
            ->orderBy('date_limite_depot', 'asc')
            ->paginate($perPage);
    }

    /**
     * Activer un concours.
     *
     * @param string $id ID du concours
     *
     * @return Concours Concours activé
     *
     * @throws ConcoursException Si le concours est introuvable ou déjà actif
     */
    public function activate(string $id): Concours
    {
        try {
            $concours = Concours::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($id);
        }

        if ($concours->est_actif) {
            throw ConcoursException::alreadyActive($id);
        }

        $concours->update(['est_actif' => true]);
        return $concours->fresh();
    }

    /**
     * Désactiver un concours.
     *
     * @param string $id ID du concours
     *
     * @return Concours Concours désactivé
     *
     * @throws ConcoursException Si le concours est introuvable ou déjà inactif
     */
    public function deactivate(string $id): Concours
    {
        try {
            $concours = Concours::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($id);
        }

        if (!$concours->est_actif) {
            throw ConcoursException::alreadyInactive($id);
        }

        $concours->update(['est_actif' => false]);
        return $concours->fresh();
    }


    /**
     * Vérifier si un concours est ouvert.
     *
     * @param string $id ID du concours
     *
     * @return bool True si le concours est ouvert, False sinon
     *
     * @throws ConcoursException Si le concours est introuvable
     */
    public function isOpen(string $id): bool
    {
        try {
            $concours = Concours::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($id);
        }

        return $concours->isOuvert();
    }

    /**
     * Obtenir les statistiques d’un concours.
     *
     * @param string $id ID du concours
     *
     * @return array Tableau contenant :
     *   - total_candidatures
     *   - candidatures_confirmees
     *   - total_paiements
     *   - paiements_valides
     *   - montant_total
     *   - nombre_sessions
     *   - sessions_actives
     *
     * @throws ConcoursException Si le concours est introuvable
     */
    public function getStats(string $id): array
    {
        try {
            $concours = Concours::with(['candidatures', 'paiements', 'sessions'])->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($id);
        }

        return [
            'total_candidatures' => $concours->candidatures()->count(),
            'candidatures_confirmees' => $concours->candidatures()->where('statut_candidature', 'VALIDE')->count(),
            'total_paiements' => $concours->paiements()->count(),
            'paiements_valides' => $concours->paiements()->where('statut', 'VERIFIED')->count(),
            'montant_total' => $concours->paiements()->where('statut', 'VERIFIED')->sum('montant'),
            'nombre_sessions' => $concours->sessions()->count(),
            'sessions_actives' => $concours->sessions()->where('est_actif', true)->count(),
        ];
    }

    /**
     * Attacher une session à un concours.
     *
     * @param string $concoursId ID du concours
     * @param string $sessionId ID de la session
     *
     * @return void
     *
     * @throws ConcoursException Si le concours est introuvable
     */
    public function attachSession(string $concoursId, string $sessionId): void
    {
        try {
            $concours = Concours::findOrFail($concoursId);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($concoursId);
        }

        if (!$concours->sessions()->where('sessions.id', $sessionId)->exists()) {
            DB::transaction(function () use ($concours, $sessionId) {
                $concours->sessions()->attach($sessionId);

                $etatOuverte = EtatSession::getByLibelle(EtatSessionEnum::OUVERTE);

                if ($etatOuverte) {
                    EtatConcoursSession::create([
                        'concours_session_concours_id' => $concours->id,
                        'concours_session_session_id' => $sessionId,
                        'etat_session_id' => $etatOuverte->id,
                        'date_etat' => now(),
                    ]);
                }
            });
        }
    }

    /**
     * Détacher une session d’un concours.
     *
     * @param string $concoursId ID du concours
     * @param string $sessionId ID de la session
     *
     * @return void
     *
     * @throws ConcoursException Si le concours est introuvable
     */
    public function detachSession(string $concoursId, string $sessionId): void
    {
        try {
            $concours = Concours::findOrFail($concoursId);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($concoursId);
        }

        DB::transaction(function () use ($concours, $sessionId) {
            EtatConcoursSession::where('concours_session_concours_id', $concours->id)
                ->where('concours_session_session_id', $sessionId)
                ->delete();

            $concours->sessions()->detach($sessionId);
        });
    }

    /**
     * Changer l’état d’une session liée à un concours.
     *
     * @param string $concoursId ID du concours
     * @param string $sessionId ID de la session
     * @param string $etatLibelle Libellé de l’état (ex: OUVERTE, FERMÉE)
     *
     * @return void
     *
     * @throws ConcoursException Si le concours est introuvable
     * @throws \Exception Si l’état est introuvable
     */
    public function changeSessionState(string $concoursId, string $sessionId, string $etatLibelle): void
    {
        try {
            $concours = Concours::findOrFail($concoursId);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($concoursId);
        }

        $etat = EtatSession::getByLibelle($etatLibelle);

        if (!$etat) {
            throw new \Exception("État '{$etatLibelle}' introuvable.", 404);
        }

        DB::transaction(function () use ($concours, $sessionId, $etat) {
            EtatConcoursSession::create([
                'concours_session_concours_id' => $concours->id,
                'concours_session_session_id' => $sessionId,
                'etat_session_id' => $etat->id,
                'date_etat' => now(),
            ]);
        });
    }

    /**
     * Attacher un concours template à une session avec configuration spécifique.
     *
     * @param string $concoursId ID du concours template
     * @param string $sessionId ID de la session
     * @param array $config Configuration spécifique (dates, places)
     *
     * @return Concours Concours attaché à la session
     *
     * @throws ConcoursException Si le concours ou la session est introuvable
     * @throws \Exception Si la session est invalide ou le concours déjà attaché
     */
    public function attachToSession(string $concoursId, string $sessionId, array $config = []): Concours
    {
        try {
            $concours = Concours::findOrFail($concoursId);
        } catch (ModelNotFoundException $e) {
            throw ConcoursException::notFound($concoursId);
        }

        // Vérifier que c'est bien un template (pas encore attaché à une session)
        if ($concours->sessions()->exists()) {
            throw new \Exception('Ce concours est déjà attaché à une session');
        }

        try {
            $session = Session::findOrFail($sessionId);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Session introuvable', 404);
        }

        if (!$session->est_actif) {
            throw new \Exception('Impossible d\'attacher à une session inactive');
        }

        // Vérifier unicité
        $existing = Concours::where('libelle_concours', $concours->libelle_concours)
            ->whereHas('sessions', function ($query) use ($sessionId) {
                $query->where('sessions.id', $sessionId);
            })->exists();

        if ($existing) {
            throw new \Exception('Un concours avec ce libellé existe déjà pour cette session');
        }

        return DB::transaction(function () use ($concours, $session, $config) {
            // Attacher la session
            $concours->sessions()->attach($session->id);

            // Créer l'état par défaut
            $etatOuverte = EtatSession::getByLibelle(EtatSessionEnum::OUVERTE);
            if ($etatOuverte) {
                EtatConcoursSession::create([
                    'concours_session_concours_id' => $concours->id,
                    'concours_session_session_id' => $session->id,
                    'etat_session_id' => $etatOuverte->id,
                    'date_etat' => now(),
                ]);
            }

            // Appliquer la configuration spécifique si fournie
            if (!empty($config)) {
                $updateData = array_intersect_key($config, array_flip([
                    'date_examen',
                    'date_limite_depot',
                    'nbre_max_places',
                    'est_actif'
                ]));
                if (!empty($updateData)) {
                    $concours->update($updateData);
                }
            }

            return $concours->fresh(['sessions']);
        });
    }

    /**
     * Obtenir l’état courant d’une session liée à un concours.
     *
     * @param string $concoursId ID du concours
     * @param string $sessionId ID de la session
     *
     * @return string|null Libellé de l’état courant ou null si aucun état
     */
    public function getCurrentSessionState(string $concoursId, string $sessionId): ?string
    {
        $etatConcours = EtatConcoursSession::where('concours_session_concours_id', $concoursId)
            ->where('concours_session_session_id', $sessionId)
            ->recent()
            ->first();

        return $etatConcours ? $etatConcours->getLibelleEtat() : null;
    }
}
