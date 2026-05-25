<?php

namespace App\Services\Domain\Concours\Checkers;

use App\Models\Concours;
use App\Models\Filiere;
use Carbon\Carbon;

class ConcoursStatusChecker
{
    /**
     * Check if concours is open for new candidatures
     */
    public function isOpen(Concours $concours): bool
    {
        return $concours->est_actif && now()->lte($concours->date_limite_depot);
    }

    /**
     * Check if concours has complete planning
     */
    public function hasCompletePlanning(Concours $concours): bool
    {
        return $concours->plannings()->where('est_actif', true)->exists();
    }

    /**
     * Get exam start date (first scheduled exam)
     */
    public function getExamStartDate(Concours $concours): ?Carbon
    {
        return $concours->plannings()
            ->where('est_actif', true)
            ->orderBy('date_epreuve')
            ->orderBy('heure_debut')
            ->first()
            ?->date_epreuve;
    }

    /**
     * Get exam end date (last scheduled exam)
     */
    public function getExamEndDate(Concours $concours): ?Carbon
    {
        return $concours->plannings()
            ->where('est_actif', true)
            ->orderByDesc('date_epreuve')
            ->orderByDesc('heure_fin')
            ->first()
            ?->date_epreuve;
    }

    /**
     * Get formatted exam period
     * Returns "15/06/2026" for single day
     * Returns "15/06/2026 - 17/06/2026" for multiple days
     */
    public function getExamPeriod(Concours $concours): ?string
    {
        $start = $this->getExamStartDate($concours);
        $end = $this->getExamEndDate($concours);

        if (! $start) {
            return null;
        }

        if (! $end || $start->eq($end)) {
            return $start->format('d/m/Y');
        }

        return $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
    }

    /**
     * Get available places for session
     */
    public function getAvailablePlacesForSession(
        Concours $concours,
        string $sessionId,
        ?string $filiereId = null
    ): int {
        $query = $concours->belongsToMany(Filiere::class, 'concours_filiere')
            ->wherePivot('session_id', $sessionId);

        if ($filiereId) {
            $query->where('filieres.id', $filiereId);
        }

        return $query->sum('concours_filiere.nombre_places');
    }

    /**
     * Get occupied places for session (VALIDE status only)
     */
    public function getOccupiedPlacesForSession(
        Concours $concours,
        string $sessionId,
        ?string $filiereId = null
    ): int {
        $query = $concours->candidatures()
            ->where('session_id', $sessionId)
            ->where('statut_candidature', 'VALIDE');

        if ($filiereId) {
            $query->whereHas('candidat', function ($q) use ($filiereId) {
                $q->where('filiere_id', $filiereId);
            });
        }

        return $query->count();
    }

    /**
     * Get remaining places for session
     */
    public function getRemainingPlacesForSession(
        Concours $concours,
        string $sessionId,
        ?string $filiereId = null
    ): int {
        $available = $this->getAvailablePlacesForSession($concours, $sessionId, $filiereId);
        $occupied = $this->getOccupiedPlacesForSession($concours, $sessionId, $filiereId);

        return max(0, $available - $occupied);
    }

    /**
     * Check if concours can accept candidature for session
     */
    public function canAcceptCandidatureForSession(
        Concours $concours,
        string $sessionId,
        ?string $filiereId = null
    ): bool {
        return $this->isOpen($concours) &&
          $this->getRemainingPlacesForSession($concours, $sessionId, $filiereId) > 0;
    }
}
