<?php

namespace App\Services\Domain\Examen\Repositories;

use App\Enums\StatutCandidature;
use App\Models\Candidature;
use App\Models\ResultatFinal;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ResultatRepository
{
    /**
     * Récupérer les candidatures éligibles pour le calcul
     */
    public function getCandidaturesEligibles(string $concoursId, string $sessionId): Collection
    {
        return Candidature::where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->where('statut_candidature', StatutCandidature::VALIDE)
            ->with(['notes.epreuve', 'candidat.filiere'])
            ->get();
    }

    /**
     * Récupérer les résultats pour une filière
     */
    public function getResultatsParFiliere(string $concoursId, string $filiereId, string $sessionId): Collection
    {
        return ResultatFinal::whereHas('candidature', function ($q) use ($concoursId, $filiereId, $sessionId) {
            $q->where('concours_id', $concoursId)
                ->where('session_id', $sessionId)
                ->whereHas('candidat', function ($sq) use ($filiereId) {
                    $sq->where('filiere_id', $filiereId);
                });
        })
            ->with('candidature.candidat.utilisateur')
            ->orderBy('moyenne_generale', 'desc')
            ->get();
    }

    /**
     * Récupérer le résultat d'un candidat
     */
    public function getResultatCandidat(string $candidatureId): ?ResultatFinal
    {
        return ResultatFinal::with(['candidature.candidat.utilisateur', 'candidature.filiere'])
            ->where('candidature_id', $candidatureId)
            ->first();
    }

    /**
     * Récupérer le classement pour une filière
     */
    public function getClassement(string $concoursId, string $sessionId, string $filiereId, int $perPage = 50): LengthAwarePaginator
    {
        return ResultatFinal::whereHas('candidature', function ($q) use ($concoursId, $sessionId, $filiereId) {
            $q->where('concours_id', $concoursId)
                ->where('session_id', $sessionId)
                ->whereHas('candidat', function ($sq) use ($filiereId) {
                    $sq->where('filiere_id', $filiereId);
                });
        })
            ->with(['candidature.candidat.utilisateur', 'candidature.candidat.filiere'])
            ->orderBy('rang')
            ->paginate($perPage);
    }

    /**
     * Récupérer tous les résultats (paginé)
     */
    public function getResultats(
        string $concoursId,
        string $sessionId,
        ?string $filiereId = null,
        int $perPage = 100
    ): LengthAwarePaginator {
        $query = ResultatFinal::whereHas('candidature', function ($q) use ($concoursId, $sessionId, $filiereId) {
            $q->where('concours_id', $concoursId)
                ->where('session_id', $sessionId);

            if ($filiereId) {
                $q->whereHas('candidat', function ($sq) use ($filiereId) {
                    $sq->where('filiere_id', $filiereId);
                });
            }
        })
            ->with(['candidature.candidat.utilisateur', 'candidature.candidat.filiere'])
            ->orderBy('moyenne_generale', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Récupérer les résultats par ordre de mérite (rang asc)
     */
    public function getResultatsParOrdreDeMerite(string $concoursId, string $sessionId): Collection
    {
        return ResultatFinal::whereHas('candidature', function ($q) use ($concoursId, $sessionId) {
            $q->where('concours_id', $concoursId)
                ->where('session_id', $sessionId);
        })
            ->with([
                'candidature.candidat.utilisateur',
                'candidature.concours',
                'candidature.session',
            ])
            ->orderByRaw('CASE WHEN rang IS NULL THEN 1 ELSE 0 END')
            ->orderBy('rang')
            ->orderBy('moyenne_generale', 'desc')
            ->get();
    }

    /**
     * Marquer les résultats comme publiés
     */
    public function publierResultats(string $concoursId, string $sessionId): int
    {
        return ResultatFinal::whereHas('candidature', function ($q) use ($concoursId, $sessionId) {
            $q->where('concours_id', $concoursId)
                ->where('session_id', $sessionId);
        })->update([
            'date_publication' => now(),
        ]);
    }
}
