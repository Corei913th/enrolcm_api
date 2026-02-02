<?php

namespace App\Services\Domain\Candidature;

use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Filiere;
use Illuminate\Support\Collection;


class FiliereValidationService
{
  /**
   * Vérifier si une filière est valide pour un concours donné
   */
  public function estFiliereValideePourConcours(string $filiereId, string $concoursId, ?string $sessionId = null): bool
  {
    $query = Concours::where('id', $concoursId)
      ->whereHas('filieres', function ($q) use ($filiereId) {
        $q->where('filieres.id', $filiereId);
      });

    // Si session_id est fourni, filtrer par session
    if ($sessionId) {
      $query->whereHas('filieres', function ($q) use ($filiereId, $sessionId) {
        $q->where('filieres.id', $filiereId)
          ->wherePivot('session_id', $sessionId);
      });
    }

    return $query->exists();
  }

  /**
   * Récupérer les filières disponibles pour un concours
   */
  public function getFilieresDisponiblesPourConcours(string $concoursId, ?string $sessionId = null): Collection
  {
    $concours = Concours::find($concoursId);

    if (!$concours) {
      return collect();
    }

    if ($sessionId) {
      return $concours->filieresParSession($sessionId)->get();
    }

    return $concours->filieres;
  }

  /**
   * Vérifier si un candidat peut choisir une filière pour sa candidature
   */
  public function candidatPeutChoisirFiliere(Candidat $candidat, string $filiereId, Candidature $candidature): array
  {
    // Vérifier que la filière existe
    $filiere = Filiere::find($filiereId);
    if (!$filiere) {
      return [
        'valide' => false,
        'message' => 'La filière sélectionnée n\'existe pas.'
      ];
    }

    if (!$filiere->est_actif) {
      return [
        'valide' => false,
        'message' => 'La filière sélectionnée n\'est pas active.'
      ];
    }

    $estAttachee = $this->estFiliereValideePourConcours(
      $filiereId,
      $candidature->concours_id,
      $candidature->session_id
    );

    if (!$estAttachee) {
      return [
        'valide' => false,
        'message' => 'La filière sélectionnée n\'est pas disponible pour ce concours.'
      ];
    }

    $placesDisponibles = $this->getPlacesDisponibles($filiereId, $candidature->concours_id, $candidature->session_id);
    if ($placesDisponibles <= 0) {
      return [
        'valide' => false,
        'message' => 'Il n\'y a plus de places disponibles pour cette filière.'
      ];
    }

    return [
      'valide' => true,
      'message' => 'La filière est valide.',
      'places_disponibles' => $placesDisponibles
    ];
  }

  /**
   * Récupérer le nombre de places disponibles pour une filière
   */
  public function getPlacesDisponibles(string $filiereId, string $concoursId, ?string $sessionId = null): int
  {
    $concours = Concours::find($concoursId);
    if (!$concours) {
      return 0;
    }

    $filiereRelation = $sessionId
      ? $concours->filieresParSession($sessionId)->where('filieres.id', $filiereId)->first()
      : $concours->filieres()->where('filieres.id', $filiereId)->first();

    if (!$filiereRelation) {
      return 0;
    }

    $nombrePlaces = $filiereRelation->pivot->nombre_places ?? 0;

    // Compter les candidatures validées pour cette filière
    $candidaturesValidees = Candidature::where('concours_id', $concoursId)
      ->where('session_id', $sessionId)
      ->whereHas('candidat', function ($query) use ($filiereId) {
        $query->where('filiere_id', $filiereId);
      })
      ->whereIn('statut_candidature', ['VALIDE', 'DOCUMENTS_VERIFIES', 'PAIEMENT_VERIFIE'])
      ->count();

    return max(0, $nombrePlaces - $candidaturesValidees);
  }

  /**
   * Validate and update candidate's filiere
   */
  public function validateAndUpdateFiliere(Candidat $candidat, string $filiereId, Candidature $candidature): array
  {
    return runTransaction(function() use ($candidat, $filiereId, $candidature) {
      // Check filiere exists and is active
      $filiere = Filiere::find($filiereId);
      if (!$filiere) {
        return [
          'valid' => false,
          'message' => 'La filière sélectionnée n\'existe pas'
        ];
      }
      
      if (!$filiere->est_actif) {
        return [
          'valid' => false,
          'message' => 'La filière sélectionnée n\'est pas active'
        ];
      }
      
      // Check filiere is attached to concours
      $isAttached = $this->estFiliereValideePourConcours(
        $filiereId,
        $candidature->concours_id,
        $candidature->session_id
      );
      
      if (!$isAttached) {
        return [
          'valid' => false,
          'message' => 'La filière sélectionnée n\'est pas disponible pour ce concours'
        ];
      }
      
      $availablePlaces = \App\Helpers\PlaceHelper::getAvailablePlaces(
        $filiereId, 
        $candidature->concours_id, 
        $candidature->session_id,
        true // with lock
      );
      
      if ($availablePlaces <= 0) {
        return [
          'valid' => false,
          'message' => 'Il n\'y a plus de places disponibles pour cette filière'
        ];
      }
      
      $candidat->update(['filiere_id' => $filiereId]);
      
      return [
        'valid' => true,
        'message' => 'La filière a été mise à jour avec succès',
        'filiere' => $filiere,
        'available_places' => $availablePlaces - 1
      ];
      
    }, 'FiliereValidationService::validateAndUpdateFiliere');
  }
  
  /**
   * @deprecated Use validateAndUpdateFiliere() instead
   * Kept for backward compatibility
   */
  public function validerEtMettreAJourFiliere(Candidat $candidat, string $filiereId, Candidature $candidature): array
  {
    return $this->validateAndUpdateFiliere($candidat, $filiereId, $candidature);
  }

  /**
   * Récupérer les statistiques des filières pour un concours
   */
  public function getStatistiquesFilieres(string $concoursId, ?string $sessionId = null): Collection
  {
    $filieres = $this->getFilieresDisponiblesPourConcours($concoursId, $sessionId);

    return $filieres->map(function ($filiere) use ($concoursId, $sessionId) {
      $nombrePlaces = $filiere->pivot->nombre_places ?? 0;
      $candidaturesValidees = Candidature::where('concours_id', $concoursId)
        ->where('session_id', $sessionId)
        ->whereHas('candidat', function ($query) use ($filiere) {
          $query->where('filiere_id', $filiere->id);
        })
        ->whereIn('statut_candidature', ['VALIDE', 'DOCUMENTS_VERIFIES', 'PAIEMENT_VERIFIE'])
        ->count();

      $placesDisponibles = max(0, $nombrePlaces - $candidaturesValidees);
      $tauxRemplissage = $nombrePlaces > 0 ? ($candidaturesValidees / $nombrePlaces) * 100 : 0;

      return [
        'filiere_id' => $filiere->id,
        'code_filiere' => $filiere->code_filiere,
        'libelle_filiere' => $filiere->libelle_filiere,
        'nombre_places' => $nombrePlaces,
        'candidatures_validees' => $candidaturesValidees,
        'places_disponibles' => $placesDisponibles,
        'taux_remplissage' => round($tauxRemplissage, 2),
        'est_complet' => $placesDisponibles <= 0,
      ];
    });
  }
}
