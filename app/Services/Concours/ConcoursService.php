<?php

namespace App\Services\Concours;

use App\Models\Concours;
use App\Models\EtatSession;
use App\Models\EtatConcoursSession;
use App\DTOs\Concours\CreateConcoursDTO;
use App\DTOs\Concours\UpdateConcoursDTO;
use App\Exceptions\ConcoursException;
use App\Enums\EtatSession as EtatSessionEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ConcoursService
{
    public function create(CreateConcoursDTO $dto): Concours
    {
        if ($dto->date_limite_depot <= $dto->date_debut) {
            throw ConcoursException::invalidDateRange();
        }

        return DB::transaction(function () use ($dto) {
            // Create concours
            $concours = Concours::create($dto->toArray());

            // Attach to session if provided
            if (isset($dto->session_id)) {
                $concours->sessions()->attach($dto->session_id);

                // Create default state "OUVERTE" for concours-session
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

    public function update(string $id, UpdateConcoursDTO $dto): Concours
    {
        $concours = Concours::find($id);
        
        if (!$concours) {
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

    public function delete(string $id): bool
    {
        $concours = Concours::find($id);
        
        if (!$concours) {
            throw ConcoursException::notFound($id);
        }

        if ($concours->candidatures()->where('statut_inscription', 'ACTIF')->exists()) {
            throw ConcoursException::hasActiveInscriptions($id);
        }

        return DB::transaction(function () use ($concours) {
            return $concours->delete();
        });
    }

    public function getById(string $id): Concours
    {
        $concours = Concours::with(['specConcours', 'filieres', 'configurationPaiement', 'sessions'])->find($id);
        
        if (!$concours) {
            throw ConcoursException::notFound($id);
        }

        return $concours;
    }

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

    public function getAvailableConcours(int $perPage = 20): LengthAwarePaginator
    {
        return Concours::with(['configurationPaiement', 'filieres', 'sessions'])
            ->where('est_actif', true)
            ->whereDate('date_limite_depot', '>=', now())
            ->orderBy('date_limite_depot', 'asc')
            ->paginate($perPage);
    }

    public function activate(string $id): Concours
    {
        $concours = Concours::find($id);
        
        if (!$concours) {
            throw ConcoursException::notFound($id);
        }

        if ($concours->est_actif) {
            throw ConcoursException::alreadyActive($id);
        }

        $concours->update(['est_actif' => true]);
        return $concours->fresh();
    }

    public function deactivate(string $id): Concours
    {
        $concours = Concours::find($id);
        
        if (!$concours) {
            throw ConcoursException::notFound($id);
        }

        if (!$concours->est_actif) {
            throw ConcoursException::alreadyInactive($id);
        }

        $concours->update(['est_actif' => false]);
        return $concours->fresh();
    }

    public function isOpen(string $id): bool
    {
        $concours = Concours::find($id);
        
        if (!$concours) {
            throw ConcoursException::notFound($id);
        }

        return $concours->isOuvert();
    }

    public function getStats(string $id): array
    {
        $concours = Concours::with(['candidatures', 'paiements', 'sessions'])->find($id);
        
        if (!$concours) {
            throw ConcoursException::notFound($id);
        }

        return [
            'total_candidatures' => $concours->candidatures()->count(),
            'candidatures_confirmees' => $concours->candidatures()->where('statut_inscription', 'ACTIF')->count(),
            'total_paiements' => $concours->paiements()->count(),
            'paiements_valides' => $concours->paiements()->where('statut', 'VERIFIED')->count(),
            'montant_total' => $concours->paiements()->where('statut', 'VERIFIED')->sum('montant'),
            'nombre_sessions' => $concours->sessions()->count(),
            'sessions_actives' => $concours->sessions()->where('est_actif', true)->count(),
        ];
    }

    public function attachSession(string $concoursId, string $sessionId): void
    {
        $concours = Concours::find($concoursId);
        
        if (!$concours) {
            throw ConcoursException::notFound($concoursId);
        }

        if (!$concours->sessions()->where('sessions.id', $sessionId)->exists()) {
            DB::transaction(function () use ($concours, $sessionId) {
                $concours->sessions()->attach($sessionId);

                // Create default state "OUVERTE"
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

    public function detachSession(string $concoursId, string $sessionId): void
    {
        $concours = Concours::find($concoursId);
        
        if (!$concours) {
            throw ConcoursException::notFound($concoursId);
        }

        DB::transaction(function () use ($concours, $sessionId) {
            // Delete associated states
            EtatConcoursSession::where('concours_session_concours_id', $concours->id)
                ->where('concours_session_session_id', $sessionId)
                ->delete();

            // Detach session
            $concours->sessions()->detach($sessionId);
        });
    }

    public function changeSessionState(string $concoursId, string $sessionId, string $etatLibelle): void
    {
        $concours = Concours::find($concoursId);
        
        if (!$concours) {
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

    public function getCurrentSessionState(string $concoursId, string $sessionId): ?string
    {
        $etatConcours = EtatConcoursSession::where('concours_session_concours_id', $concoursId)
            ->where('concours_session_session_id', $sessionId)
            ->recent()
            ->first();

        return $etatConcours ? $etatConcours->getLibelleEtat() : null;
    }
}
