<?php

namespace App\Services\Domain\Examen\Calculators;

use App\Enums\Mention;
use App\Models\Candidature;
use App\Models\PlanningEpreuve;
use Illuminate\Support\Collection;

/**
 * Calculateur de moyenne générale
 */
class MoyenneCalculator
{
    /**
     * Calculer la moyenne générale d'un candidat
     */
    public function calculer(Candidature $candidature, Collection $notes): array
    {
        if ($notes->isEmpty()) {
            throw new \Exception("Aucune note définitive trouvée pour la candidature {$candidature->id}");
        }

        $plannings = PlanningEpreuve::where('concours_id', $candidature->concours_id)
            ->whereIn('epreuve_id', $notes->pluck('epreuve_id'))
            ->get()
            ->keyBy('epreuve_id');

        $totalNotes = 0;
        $totalCoefficients = 0;

        foreach ($notes as $note) {
            $planning = $plannings->get($note->epreuve_id);
            $coefficient = $planning?->coefficient ?? $note->epreuve->coefficient_defaut ?? 1;

            $totalNotes += $note->valeur * $coefficient;
            $totalCoefficients += $coefficient;
        }

        $moyenneGenerale = $totalCoefficients > 0 ? $totalNotes / $totalCoefficients : 0;

        return [
            'moyenne_generale' => round($moyenneGenerale, 2),
            'total_point' => round($totalNotes, 2),
            'total_coefficients' => $totalCoefficients,
        ];
    }

    /**
     * Calculer la mention selon la moyenne
     */
    public function calculerMention(float $moyenne): ?Mention
    {
        if ($moyenne >= 18) {
            return Mention::EXCELLENT;
        }
        if ($moyenne >= 16) {
            return Mention::TRES_BIEN;
        }
        if ($moyenne >= 14) {
            return Mention::BIEN;
        }
        if ($moyenne >= 12) {
            return Mention::ASSEZ_BIEN;
        }
        if ($moyenne >= 10) {
            return Mention::PASSABLE;
        }

        return Mention::INSUFFISANT; // ou null si tu y tiens
    }
}
