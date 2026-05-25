<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class NoteHelper
{
    /**
     * Calculate weighted average with coefficients (optimized single query)
     */
    public static function calculateWeightedAverage(string $candidatureId): array
    {
        $result = DB::table('notes')
            ->join('epreuves', 'notes.epreuve_id', '=', 'epreuves.id_epreuve')
            ->where('notes.candidature_id', $candidatureId)
            ->where('notes.statut', 'VALIDEE')
            ->select([
                DB::raw('SUM(notes.valeur * COALESCE(epreuves.coefficient, 1)) as total_points'),
                DB::raw('SUM(COALESCE(epreuves.coefficient, 1)) as total_coefficients'),
                DB::raw('COUNT(*) as validated_count'),
            ])
            ->first();

        if (! $result || $result->validated_count == 0) {
            return [
                'average' => 0,
                'total_points' => 0,
                'total_coefficients' => 0,
                'validated_count' => 0,
                'is_eliminated' => false,
            ];
        }

        $average = $result->total_coefficients > 0
            ? round($result->total_points / $result->total_coefficients, 2)
            : 0;

        return [
            'average' => $average,
            'total_points' => (float) $result->total_points,
            'total_coefficients' => (float) $result->total_coefficients,
            'validated_count' => (int) $result->validated_count,
            'is_eliminated' => false, // Will be checked separately
        ];
    }

    /**
     * Check for eliminatory notes (optimized single query)
     */
    public static function checkEliminatoryNotes(string $candidatureId): array
    {
        $eliminatoryNote = DB::table('notes')
            ->join('epreuves', 'notes.epreuve_id', '=', 'epreuves.id_epreuve')
            ->where('notes.candidature_id', $candidatureId)
            ->where('notes.statut', 'VALIDEE')
            ->where('notes.est_eliminatoire', true)
            ->whereRaw('notes.valeur < COALESCE(epreuves.seuil_eliminatoire, 5)')
            ->select([
                'notes.valeur',
                'epreuves.intitule',
                'epreuves.seuil_eliminatoire',
            ])
            ->first();

        if ($eliminatoryNote) {
            $threshold = $eliminatoryNote->seuil_eliminatoire ?? 5;

            return [
                'is_eliminated' => true,
                'reason' => "Eliminatory note: {$eliminatoryNote->intitule} ({$eliminatoryNote->valeur}/{$threshold})",
                'subject' => $eliminatoryNote->intitule,
                'note_value' => (float) $eliminatoryNote->valeur,
                'threshold' => (float) $threshold,
            ];
        }

        return ['is_eliminated' => false];
    }

    /**
     * Calculate complete average with elimination check (combines both methods)
     */
    public static function calculateCompleteAverage(string $candidatureId): array
    {
        // First check for eliminatory notes
        $eliminatoryCheck = self::checkEliminatoryNotes($candidatureId);

        if ($eliminatoryCheck['is_eliminated']) {
            return array_merge([
                'average' => 0,
                'total_points' => 0,
                'total_coefficients' => 0,
                'validated_count' => 0,
            ], $eliminatoryCheck);
        }

        // If not eliminated, calculate weighted average
        return self::calculateWeightedAverage($candidatureId);
    }

    /**
     * Validate note value is within bounds
     */
    public static function isValidNoteValue(float $value): bool
    {
        return $value >= 0 && $value <= 20;
    }

    /**
     * Check if note can be modified (not validated)
     */
    public static function canModifyNote(string $noteId): bool
    {
        return DB::table('notes')
            ->where('id', $noteId)
            ->where('statut', '!=', 'VALIDEE')
            ->exists();
    }
}
