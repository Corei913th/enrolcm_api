<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PlaceHelper
{
    /**
     * Get available places for a filiere (with optional lock)
     * 
     * @param string $filiereId
     * @param string $concoursId
     * @param string $sessionId
     * @param bool $withLock Use pessimistic lock for race condition prevention
     * @return int
     */
    public static function getAvailablePlaces(
        string $filiereId, 
        string $concoursId, 
        string $sessionId,
        bool $withLock = false
    ): int {
        // Get configured places
        $query = DB::table('concours_filieres')
            ->where('concours_id', $concoursId)
            ->where('filiere_id', $filiereId)
            ->where('session_id', $sessionId);
        
        if ($withLock) {
            $query->lockForUpdate();
        }
        
        $config = $query->first();
        
        if (!$config) {
            return 0;
        }
        
        $configuredPlaces = $config->nombre_places ?? 0;
        
        // Count validated candidatures
        $validatedCount = DB::table('candidatures')
            ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
            ->where('candidatures.concours_id', $concoursId)
            ->where('candidatures.session_id', $sessionId)
            ->where('candidats.filiere_id', $filiereId)
            ->whereIn('candidatures.statut_candidature', ['VALIDE', 'DOCUMENTS_VERIFIES', 'PAIEMENT_VERIFIE'])
            ->count();
        
        return max(0, $configuredPlaces - $validatedCount);
    }
    
    /**
     * Check if over-booking exists for a concours/session
     * 
     * @param string $concoursId
     * @param string $sessionId
     * @return array Array of issues (empty if no over-booking)
     */
    public static function checkOverBooking(
        string $concoursId, 
        string $sessionId
    ): array {
        $issues = [];
        
        $filieres = DB::table('concours_filieres')
            ->where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->get();
        
        foreach ($filieres as $filiere) {
            $configuredPlaces = $filiere->nombre_places ?? 0;
            
            $validatedCount = DB::table('candidatures')
                ->join('candidats', 'candidatures.candidat_id', '=', 'candidats.utilisateur_id')
                ->where('candidatures.concours_id', $concoursId)
                ->where('candidatures.session_id', $sessionId)
                ->where('candidats.filiere_id', $filiere->filiere_id)
                ->whereIn('candidatures.statut_candidature', ['VALIDE'])
                ->count();
            
            if ($validatedCount > $configuredPlaces) {
                $issues[] = [
                    'filiere_id' => $filiere->filiere_id,
                    'configured_places' => $configuredPlaces,
                    'validated_count' => $validatedCount,
                    'surplus' => $validatedCount - $configuredPlaces,
                ];
            }
        }
        
        return $issues;
    }
    
    /**
     * Get total available places for a concours/session
     * 
     * @param string $concoursId
     * @param string $sessionId
     * @return int
     */
    public static function getTotalAvailablePlaces(string $concoursId, string $sessionId): int
    {
        $configured = DB::table('concours_filieres')
            ->where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->sum('nombre_places');
        
        $validated = DB::table('candidatures')
            ->where('concours_id', $concoursId)
            ->where('session_id', $sessionId)
            ->whereIn('statut_candidature', ['VALIDE', 'DOCUMENTS_VERIFIES', 'PAIEMENT_VERIFIE'])
            ->count();
        
        return max(0, $configured - $validated);
    }
    
    /**
     * Check if filiere is full
     * 
     * @param string $filiereId
     * @param string $concoursId
     * @param string $sessionId
     * @return bool
     */
    public static function isFiliereFull(
        string $filiereId, 
        string $concoursId, 
        string $sessionId
    ): bool {
        return self::getAvailablePlaces($filiereId, $concoursId, $sessionId) <= 0;
    }
    
    /**
     * Log over-booking issues
     * 
     * @param string $concoursId
     * @param string $sessionId
     * @return void
     */
    public static function logOverBookingIssues(string $concoursId, string $sessionId): void
    {
        $issues = self::checkOverBooking($concoursId, $sessionId);
        
        if (!empty($issues)) {
            Log::critical('OVER-BOOKING DETECTED', [
                'concours_id' => $concoursId,
                'session_id' => $sessionId,
                'issues' => $issues,
                'total_surplus' => array_sum(array_column($issues, 'surplus')),
            ]);
        }
    }
}
