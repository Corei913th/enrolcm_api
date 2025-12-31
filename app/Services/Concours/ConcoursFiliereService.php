<?php

namespace App\Services\Concours;

use App\Models\Concours;
use App\Models\Filiere;
use App\Models\ConcoursFiliere;
use App\Models\Candidature;
use App\Exceptions\ConcoursException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ConcoursFiliereService
{
  /**
   * Valider que le nombre de places est valide.
   *
   * @param int $nombrePlaces Nombre de places à valider
   *
   * @return void
   *
   * @throws ConcoursException Si le nombre de places est <= 0
   */
  private function validateNombrePlaces(int $nombrePlaces): void
  {
    if ($nombrePlaces <= 0) {
      throw ConcoursException::invalidNombrePlaces();
    }
  }

  /**
   * Trouver un concours ou lever une exception.
   *
   * @param string $concoursId ID du concours
   *
   * @return Concours Concours trouvé
   *
   * @throws ConcoursException Si le concours est introuvable
   */
  private function findConcoursOrFail(string $concoursId): Concours
  {
    try {
      return Concours::findOrFail($concoursId);
    } catch (ModelNotFoundException $e) {
      throw ConcoursException::notFound($concoursId);
    }
  }

  /**
   * Trouver une filière ou lever une exception.
   *
   * @param string $filiereId ID de la filière
   *
   * @return Filiere Filière trouvée
   *
   * @throws ConcoursException Si la filière est introuvable
   */
  private function findFiliereOrFail(string $filiereId): Filiere
  {
    try {
      return Filiere::findOrFail($filiereId);
    } catch (ModelNotFoundException $e) {
      throw ConcoursException::filiereNotFound($filiereId);
    }
  }

  /**
   * Vérifier que le concours est attaché à la session.
   *
   * @param Concours $concours Concours à vérifier
   * @param string $sessionId ID de la session
   *
   * @return void
   *
   * @throws ConcoursException Si le concours n'est pas attaché à la session
   */
  private function ensureConcoursAttachedToSession(Concours $concours, string $sessionId): void
  {
    if (!$concours->sessions()->where('sessions.id', $sessionId)->exists()) {
      throw ConcoursException::concoursNotAttachedToSession($concours->id, $sessionId);
    }
  }

  /**
   * Vérifier que la filière est attachée au concours pour la session.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $filiereId ID de la filière
   *
   * @return ConcoursFiliere Relation trouvée
   *
   * @throws ConcoursException Si la filière n'est pas attachée
   */
  private function ensureFiliereAttached(string $concoursId, string $sessionId, string $filiereId): ConcoursFiliere
  {
    $relation = $this->getRelation($concoursId, $sessionId, $filiereId);

    if (!$relation) {
      throw ConcoursException::filiereNotAttached($filiereId, $concoursId, $sessionId);
    }

    return $relation;
  }

  /**
   * Vérifier qu'il n'y a pas de candidatures actives pour la filière.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $filiereId ID de la filière
   *
   * @return void
   *
   * @throws ConcoursException Si des candidatures actives existent
   */
  private function ensureNoActiveCandidatures(string $concoursId, string $sessionId, string $filiereId): void
  {
    $hasActive = Candidature::where('concours_id', $concoursId)
      ->where('session_id', $sessionId)
      ->whereHas('candidat', function ($query) use ($filiereId) {
        $query->where('filiere_id', $filiereId);
      })
      ->where('statut_candidature', 'VALIDE')
      ->exists();

    if ($hasActive) {
      throw ConcoursException::hasActiveCandidaturesForFiliere($filiereId);
    }
  }

  /**
   * Attacher une filière à un concours pour une session spécifique.
   *
   * Si la filière est déjà attachée, le nombre de places sera mis à jour.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $filiereId ID de la filière
   * @param int $nombrePlaces Nombre de places disponibles (doit être > 0)
   *
   * @return ConcoursFiliere Relation créée ou mise à jour
   *
   * @throws ConcoursException Si le concours, la filière est introuvable, le concours n'est pas attaché à la session, ou le nombre de places est invalide
   */
  public function attachFiliere(string $concoursId, string $sessionId, string $filiereId, int $nombrePlaces): ConcoursFiliere
  {
    $concours = $this->findConcoursOrFail($concoursId);
    $this->findFiliereOrFail($filiereId);
    $this->ensureConcoursAttachedToSession($concours, $sessionId);
    $this->validateNombrePlaces($nombrePlaces);

    return DB::transaction(function () use ($concoursId, $sessionId, $filiereId, $nombrePlaces) {
      // Vérifier si la relation existe déjà
      $existing = $this->getRelation($concoursId, $sessionId, $filiereId);

      if ($existing) {
        // Mettre à jour le nombre de places
        $existing->update(['nombre_places' => $nombrePlaces]);
        return $existing->fresh();
      }

      // Créer la relation
      $data = [
        'concours_id' => $concoursId,
        'filiere_id' => $filiereId,
        'nombre_places' => $nombrePlaces,
      ];

      // Ajouter session_id si la colonne existe
      if (Schema::hasColumn('concours_filiere', 'session_id')) {
        $data['session_id'] = $sessionId;
      }

      return ConcoursFiliere::create($data);
    });
  }

  /**
   * Détacher une filière d'un concours pour une session.
   *
   * Impossible de détacher si des candidatures actives existent pour cette filière.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $filiereId ID de la filière
   *
   * @return bool True si détaché avec succès
   *
   * @throws ConcoursException Si le concours est introuvable, la filière n'est pas attachée, ou s'il y a des candidatures actives
   */
  public function detachFiliere(string $concoursId, string $sessionId, string $filiereId): bool
  {
    $this->findConcoursOrFail($concoursId);
    $this->ensureNoActiveCandidatures($concoursId, $sessionId, $filiereId);

    return DB::transaction(function () use ($concoursId, $sessionId, $filiereId) {
      $relation = $this->ensureFiliereAttached($concoursId, $sessionId, $filiereId);
      return $relation->delete();
    });
  }

  /**
   * Lister toutes les filières d'un concours pour une session.
   *
   * Retourne chaque filière avec ses statistiques :
   * - nombre_places : Nombre total de places
   * - candidatures_validees : Nombre de candidatures validées
   * - places_restantes : Places encore disponibles
   * - taux_remplissage : Pourcentage de remplissage (0-100)
   * - peut_accepter_candidatures : Booléen indiquant si de nouvelles candidatures sont possibles
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   *
   * @return \Illuminate\Support\Collection Collection de tableaux contenant les filières avec leurs statistiques
   *
   * @throws ConcoursException Si le concours est introuvable
   */
  public function listFilieres(string $concoursId, string $sessionId)
  {
    $concours = $this->findConcoursOrFail($concoursId);

    $filieres = $concours->filieresParSession($sessionId)->get();

    return $filieres->map(function ($filiere) use ($concoursId, $sessionId) {
      $pivot = $filiere->pivot;
      $nombreCandidatures = $this->getNombreCandidatures($concoursId, $sessionId, $filiere->id);
      $placesRestantes = max(0, $pivot->nombre_places - $nombreCandidatures);

      return [
        'id' => $filiere->id,
        'code_filiere' => $filiere->code_filiere,
        'libelle_filiere' => $filiere->libelle_filiere,
        'desc_filiere' => $filiere->desc_filiere,
        'nombre_places' => $pivot->nombre_places,
        'candidatures_validees' => $nombreCandidatures,
        'places_restantes' => $placesRestantes,
        'taux_remplissage' => $this->calculerTauxRemplissage($nombreCandidatures, $pivot->nombre_places),
        'peut_accepter_candidatures' => $placesRestantes > 0,
      ];
    });
  }

  /**
   * Obtenir les statistiques détaillées d'une filière pour un concours et une session.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $filiereId ID de la filière
   *
   * @return array Tableau contenant les statistiques détaillées
   *
   * @throws ConcoursException Si le concours ou la filière est introuvable, ou si la filière n'est pas attachée
   */
  public function getStats(string $concoursId, string $sessionId, string $filiereId): array
  {
    $this->findConcoursOrFail($concoursId);
    $relation = $this->ensureFiliereAttached($concoursId, $sessionId, $filiereId);
    $filiere = $this->findFiliereOrFail($filiereId);
    $nombreCandidatures = $this->getNombreCandidatures($concoursId, $sessionId, $filiereId);
    $placesRestantes = max(0, $relation->nombre_places - $nombreCandidatures);

    // Statistiques par statut
    $statsParStatut = Candidature::where('concours_id', $concoursId)
      ->where('session_id', $sessionId)
      ->whereHas('candidat', function ($query) use ($filiereId) {
        $query->where('filiere_id', $filiereId);
      })
      ->selectRaw('statut_candidature, COUNT(*) as count')
      ->groupBy('statut_candidature')
      ->pluck('count', 'statut_candidature')
      ->toArray();

    return [
      'filiere' => [
        'id' => $filiere->id,
        'code_filiere' => $filiere->code_filiere,
        'libelle_filiere' => $filiere->libelle_filiere,
        'desc_filiere' => $filiere->desc_filiere,
      ],
      'places' => [
        'total' => $relation->nombre_places,
        'occupees' => $nombreCandidatures,
        'restantes' => $placesRestantes,
        'taux_remplissage' => $this->calculerTauxRemplissage($nombreCandidatures, $relation->nombre_places),
      ],
      'candidatures' => [
        'total' => Candidature::where('concours_id', $concoursId)
          ->where('session_id', $sessionId)
          ->whereHas('candidat', function ($query) use ($filiereId) {
            $query->where('filiere_id', $filiereId);
          })
          ->count(),
        'validees' => $nombreCandidatures,
        'par_statut' => $statsParStatut,
      ],
      'peut_accepter_candidatures' => $placesRestantes > 0,
    ];
  }

  /**
   * Mettre à jour le nombre de places d'une filière.
   *
   * Le nouveau nombre de places ne peut pas être inférieur au nombre de candidatures validées.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $filiereId ID de la filière
   * @param int $nombrePlaces Nouveau nombre de places (doit être > 0 et >= candidatures validées)
   *
   * @return ConcoursFiliere Relation mise à jour
   *
   * @throws ConcoursException Si le concours est introuvable, la filière n'est pas attachée, le nombre de places est invalide, ou inférieur aux candidatures validées
   */
  public function updateNombrePlaces(string $concoursId, string $sessionId, string $filiereId, int $nombrePlaces): ConcoursFiliere
  {
    $this->validateNombrePlaces($nombrePlaces);
    $relation = $this->ensureFiliereAttached($concoursId, $sessionId, $filiereId);

    $nombreCandidatures = $this->getNombreCandidatures($concoursId, $sessionId, $filiereId);

    if ($nombrePlaces < $nombreCandidatures) {
      throw ConcoursException::placesInferiorToCandidatures($nombreCandidatures);
    }

    $relation->update(['nombre_places' => $nombrePlaces]);

    return $relation->fresh();
  }

  /**
   * Obtenir la relation concours-filiere-session.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $filiereId ID de la filière
   *
   * @return ConcoursFiliere|null Relation trouvée ou null
   */
  private function getRelation(string $concoursId, string $sessionId, string $filiereId): ?ConcoursFiliere
  {
    $query = ConcoursFiliere::where('concours_id', $concoursId)
      ->where('filiere_id', $filiereId);

    if (Schema::hasColumn('concours_filiere', 'session_id')) {
      $query->where('session_id', $sessionId);
    }

    return $query->first();
  }

  /**
   * Calculer le taux de remplissage en pourcentage.
   *
   * @param int $occupees Nombre de places occupées
   * @param int $total Nombre total de places
   *
   * @return float Taux de remplissage en pourcentage (0-100)
   */
  private function calculerTauxRemplissage(int $occupees, int $total): float
  {
    return $total > 0 ? round(($occupees / $total) * 100, 2) : 0;
  }

  /**
   * Obtenir le nombre de candidatures validées pour une filière.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @param string $filiereId ID de la filière
   *
   * @return int Nombre de candidatures avec statut VALIDE
   */
  private function getNombreCandidatures(string $concoursId, string $sessionId, string $filiereId): int
  {
    return Candidature::where('concours_id', $concoursId)
      ->where('session_id', $sessionId)
      ->whereHas('candidat', function ($query) use ($filiereId) {
        $query->where('filiere_id', $filiereId);
      })
      ->where('statut_candidature', 'VALIDE')
      ->count();
  }
}
