<?php

namespace App\Services\Candidature;

use App\Enums\StatutCandidature;
use App\Enums\StatutInscription;
use App\Models\Candidature;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CandidatureService
{


  /**
   * Get candidature by id
   * @param string $candidatureId
   * @return Candidature
   * @throws Illuminate\Database\Eloquent\ModelNotFoundException;
   */
  public function getCandidatureOrFail(string $candidatureId): Candidature
  {
    try {
      return Candidature::findOrFail($candidatureId);
    } catch (ModelNotFoundException) {
      throw new DomainException('Candidature introuvable');
    }
  }

  /**
   * create a candidature
   * @param $candidat
   * @param $concoursId
   * @param $sessionActive
   * @param null $dateInscription
   * @return Candidature
   */
  public function createCandidature($candidat, $concoursId, $sessionActive, $dateInscription = null): Candidature
  {
    return Candidature::create([
      'candidat_id' => $candidat->utilisateur_id,
      'concours_id' => $concoursId,
      'session_id' => $sessionActive->id,
      'statut_inscription' => StatutInscription::ACTIF,
      'statut_candidature' => StatutCandidature::SOUMISE,
      'date_candidature' => now(),
      'date_inscription' => $dateInscription ?? now(),
    ]);
  }
}
