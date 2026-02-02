<?php

namespace App\Services\Domain\Examen\Calculators;

use App\Models\Candidature;
use Illuminate\Support\Collection;

/**
 * Calculateur de moyenne générale
 */
class MoyenneCalculator
{
  /**
   * Calculer la moyenne générale d'un candidat
   * @param Candidature $candidature
   * @param Collection $notes
   * @return array
   */
  public function calculer(Candidature $candidature, Collection $notes): array
  {
    if ($notes->isEmpty()) {
      throw new \Exception("Aucune note définitive trouvée pour la candidature {$candidature->id}");
    }

    $plannings = \App\Models\PlanningEpreuve::where('concours_id', $candidature->concours_id)
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
   * @param float $moyenne
   * @return \App\Enums\Mention|null
   */
  public function calculerMention(float $moyenne): ?\App\Enums\Mention
{
    if ($moyenne >= 18) return \App\Enums\Mention::EXCELLENT;
    if ($moyenne >= 16) return \App\Enums\Mention::TRES_BIEN;
    if ($moyenne >= 14) return \App\Enums\Mention::BIEN;
    if ($moyenne >= 12) return \App\Enums\Mention::ASSEZ_BIEN;
    if ($moyenne >= 10) return \App\Enums\Mention::PASSABLE;

    return \App\Enums\Mention::INSUFFISANT; // ou null si tu y tiens
}

}
