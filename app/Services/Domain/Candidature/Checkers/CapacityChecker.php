<?php

namespace App\Services\Domain\Candidature\Checkers;

use App\Enums\StatutCandidature;
use App\Models\Concours;
use App\Services\Domain\Concours\Checkers\ConcoursStatusChecker;

class CapacityChecker
{
    public function __construct(
        private readonly ConcoursStatusChecker $statusChecker
    ) {}

    /**
     * Get reserved places (including pending validations)
     * To prevent overbooking
     *
     * @return int Number of reserved places
     */
    public function getReservedPlaces(
        Concours $concours,
        string $sessionId,
        ?string $filiereId = null
    ): int {
        $query = $concours->candidatures()
            ->where('session_id', $sessionId)
            ->whereIn('statut_candidature', [
                StatutCandidature::VALIDE,
                StatutCandidature::SOUMISE,
                StatutCandidature::DOCUMENTS_VERIFIES,
                StatutCandidature::PAIEMENT_VERIFIE,
            ]);

        if ($filiereId) {
            $query->whereHas('candidat', function ($q) use ($filiereId) {
                $q->where('filiere_id', $filiereId);
            });
        }

        return $query->count();
    }

    /**
     * Check if concours can accept a new candidature
     *
     * @return bool True if can accept, False otherwise
     */
    public function canAcceptNewCandidature(
        Concours $concours,
        string $sessionId,
        ?string $filiereId = null
    ): bool {
        if (! $this->statusChecker->isOpen($concours)) {
            return false;
        }

        $available = $this->statusChecker->getAvailablePlacesForSession($concours, $sessionId, $filiereId);
        $reserved = $this->getReservedPlaces($concours, $sessionId, $filiereId);

        return $reserved < $available;
    }

    /**
     * Get capacity report
     *
     * @return array Capacity details
     */
    public function getCapacityReport(
        Concours $concours,
        string $sessionId,
        ?string $filiereId = null
    ): array {
        $available = $this->statusChecker->getAvailablePlacesForSession($concours, $sessionId, $filiereId);
        $occupied = $this->statusChecker->getOccupiedPlacesForSession($concours, $sessionId, $filiereId);
        $reserved = $this->getReservedPlaces($concours, $sessionId, $filiereId);
        $free = max(0, $available - $reserved);

        return [
            'available' => $available,
            'occupied' => $occupied,
            'reserved' => $reserved,
            'free' => $free,
            'can_accept' => $this->canAcceptNewCandidature($concours, $sessionId, $filiereId),
            'fill_rate' => $available > 0 ? round(($reserved / $available) * 100, 2) : 0,
        ];
    }
}
