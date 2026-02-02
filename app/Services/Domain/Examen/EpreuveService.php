<?php

namespace App\Services\Domain\Examen;

use App\Models\Epreuve;
use App\Models\Note;
use App\Exceptions\EpreuveException;
use App\Models\Concours;
use App\Traits\HasAdvancedSearch;
use App\Traits\HasOptimizedUpdate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class EpreuveService
{
  use HasAdvancedSearch, HasOptimizedUpdate;
  /**
   * Obtenir toutes les épreuves avec pagination et filtres.
   *
   * @param array $filters Filtres optionnels
   * @param int $perPage Nombre d'éléments par page
   * @return LengthAwarePaginator
   */
  public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
  {
    $query = Epreuve::query()
      ->select([
        'id_epreuve',
        'intitule',
        'session',
        'url_epreuve',
        'type_epreuve',
        'duree_en_minute',
        'est_actif',
        'created_at',
        'updated_at'
      ]);


    if (!empty($filters['search'])) {
      $this->applySearch(
        $query,
        $filters['search'],
        [
          'intitule' => 'words',
          'session' => 'partial'
        ]
      );
    }

    // Filtres simples
    $simpleFilters = [];
    if (isset($filters['type_epreuve'])) {
      $simpleFilters['type_epreuve'] = $filters['type_epreuve'];
    }
    if (isset($filters['est_actif'])) {
      $simpleFilters['est_actif'] = $filters['est_actif'];
    }
    $this->applyFilters($query, $simpleFilters);

    // Tri
    $sortBy = $filters['sort_by'] ?? 'intitule';
    $sortOrder = $filters['sort_order'] ?? 'asc';
    $this->applySort(
      $query,
      $sortBy,
      $sortOrder,
      'intitule',
      ['intitule', 'session', 'type_epreuve', 'duree_en_minute', 'created_at']
    );

    // Pagination
    $perPage = $filters['per_page'] ?? $perPage;
    return $query->paginate($perPage);
  }

  /**
   * Obtenir les épreuves pour un concours et une session via planning_epreuves.
   *
   * @param string $concoursId ID du concours
   * @param string $sessionId ID de la session
   * @return Collection
   */
  public function getEpreuvesByConcoursSession(string $concoursId, string $sessionId): Collection
  {
    return Epreuve::whereHas('plannings', function ($query) use ($concoursId, $sessionId) {
      $query->where('concours_id', $concoursId)
        ->where('session_id', $sessionId)
        ->where('est_actif', true);
    })
      ->where('est_actif', true)
      ->orderBy('intitule')
      ->get();
  }

  /**
   * Get epreuves attached to a concours (via planning_epreuves table).
   *
   * @param string $concoursId
   * @return Collection
   */
  public function getEpreuvesByConcours(string $concoursId): Collection
  {
    // Get epreuves that have planning for this concours
    return Epreuve::whereHas('plannings', function ($query) use ($concoursId) {
      $query->where('concours_id', $concoursId);
    })
      ->with(['plannings' => function ($query) use ($concoursId) {
        $query->where('concours_id', $concoursId)
          ->select('planning_epreuves.*');
      }])
      ->where('est_actif', true)
      ->orderBy('intitule')
      ->get();
  }

  /**
   * Créer une nouvelle épreuve.
   *
   * @param array $data
   * @return Epreuve
   * @throws \Exception
   */
  public function create(array $data): Epreuve
  {
    return runTransaction(function () use ($data) {
      try {

        if (isset($data['fichier_epreuve']) && $data['fichier_epreuve'] instanceof UploadedFile) {
          $file = $data['fichier_epreuve'];
          $filename = Str::slug($data['intitule']) . '-' . time() . '.' . $file->getClientOriginalExtension();
          $path = $file->storeAs('epreuves', $filename, 'public');
          $data['url_epreuve'] = Storage::url($path);
          unset($data['fichier_epreuve']);
        }

        return Epreuve::create($data);
      } catch (\Exception $e) {
        throw EpreuveException::invalidData("Erreur lors de la création de l'épreuve: " . $e->getMessage());
      }
    }, "EpreuveService::create");
  }

  /**
   * Mettre à jour une épreuve.
   *
   * @param string $epreuveId
   * @param array $data
   * @return Epreuve
   * @throws EpreuveException
   */
  public function update(string $epreuveId, array $data): Epreuve
  {
    return runTransaction(function () use ($epreuveId, $data) {
      try {
        $epreuve = Epreuve::findOrFail($epreuveId);


        if (isset($data['fichier_epreuve']) && $data['fichier_epreuve'] instanceof UploadedFile) {

          if ($epreuve->url_epreuve) {
            $oldPath = str_replace('/storage/', '', parse_url($epreuve->url_epreuve, PHP_URL_PATH));
            Storage::disk('public')->delete($oldPath);
          }

          $file = $data['fichier_epreuve'];
          $filename = Str::slug($data['intitule'] ?? $epreuve->intitule) . '-' . time() . '.' . $file->getClientOriginalExtension();
          $path = $file->storeAs('epreuves', $filename, 'public');
          $data['url_epreuve'] = Storage::url($path);
          unset($data['fichier_epreuve']);
        }

        $this->updateIfDirty($epreuve, $data);
        return $epreuve->fresh();
      } catch (ModelNotFoundException $e) {
        throw EpreuveException::notFound($epreuveId);
      } catch (\Exception $e) {
        throw EpreuveException::invalidData("Erreur lors de la mise à jour: " . $e->getMessage());
      }
    }, "EpreuveService::update");
  }

  /**
   * Supprimer une épreuve.
   *
   * @param string $epreuveId
   * @return bool
   * @throws EpreuveException
   */
  public function delete(string $epreuveId): bool
  {
    return runTransaction(function () use ($epreuveId) {
      try {
        $epreuve = Epreuve::findOrFail($epreuveId);

        if ($epreuve->notes()->exists()) {
          throw EpreuveException::cannotDelete($epreuveId, "L'épreuve a des notes associées");
        }


        if ($epreuve->url_epreuve) {
          $path = str_replace('/storage/', '', parse_url($epreuve->url_epreuve, PHP_URL_PATH));
          Storage::disk('public')->delete($path);
        }

        return $epreuve->delete();
      } catch (ModelNotFoundException $e) {
        throw EpreuveException::notFound($epreuveId);
      } catch (EpreuveException $e) {
        throw $e;
      } catch (\Exception $e) {
        throw EpreuveException::invalidData("Erreur lors de la suppression: " . $e->getMessage());
      }
    }, "EpreuveService::delete");
  }

  /**
   * Obtenir une épreuve par ID.
   *
   * @param string $epreuveId ID de l'épreuve
   * @return Epreuve
   * @throws EpreuveException
   */
  public function getEpreuveById(string $epreuveId): Epreuve
  {
    try {
      return Epreuve::findOrFail($epreuveId);
    } catch (ModelNotFoundException $e) {
      throw EpreuveException::notFound($epreuveId);
    }
  }

  /**
   * Obtenir toutes les notes pour une épreuve.
   *
   * @param string $epreuveId ID de l'épreuve
   * @return Collection
   * @throws EpreuveException
   */
  public function getNotesByEpreuve(string $epreuveId): Collection
  {

    $this->getEpreuveById($epreuveId);

    return Note::where('epreuve_id', $epreuveId)
      ->with(['candidature.candidat.utilisateur'])
      ->orderBy('valeur', 'desc')
      ->get();
  }

  /**
   * Liste des épreuves disponibles (non attachées via planning) pour un concours.
   */
  public function getEpreuvesDisponibles(string $concoursId): Collection
  {
    // Récupérer les IDs des épreuves déjà planifiées pour ce concours
    $attachedIds = DB::table('planning_epreuves')
      ->where('concours_id', $concoursId)
      ->pluck('epreuve_id')
      ->toArray();

    // Retourner les épreuves non attachées
    return Epreuve::whereNotIn('id_epreuve', $attachedIds)
      ->where('est_actif', true)
      ->orderBy('intitule')
      ->get();
  }

  /**
   * Attacher une épreuve à un concours via planning.
   * Crée une entrée de planning avec date/heure par défaut.
   */
  public function attachEpreuveToConcours(
    string $concoursId,
    string $epreuveId,
    ?string $dateEpreuve = null,
    ?string $heureDebut = null,
    ?string $heureFin = null
  ): Epreuve {
    return runTransaction(function () use ($concoursId, $epreuveId, $dateEpreuve, $heureDebut, $heureFin) {
      $concours = Concours::with('sessions')->findOrFail($concoursId);
      $session = $concours->sessions->first();

      if (!$session) {
        throw new \Exception('Ce concours n\'a pas de session associée');
      }

      // Vérifier si déjà planifié
      $exists = DB::table('planning_epreuves')
        ->where('concours_id', $concoursId)
        ->where('epreuve_id', $epreuveId)
        ->where('session_id', $session->id)
        ->exists();

      if ($exists) {
        throw new \Exception('Cette épreuve est déjà planifiée pour ce concours');
      }

      $epreuve = Epreuve::findOrFail($epreuveId);

      // Utiliser la date d'examen du concours par défaut ou celle fournie
      $date = $dateEpreuve ?? $concours->date_examen ?? now()->addDays(30)->format('Y-m-d');
      $debut = $heureDebut ?? '08:00:00';

      // Calculer heure fin basée sur durée de l'épreuve
      if (!$heureFin) {
        $heureFinCalculee = Carbon::parse($debut)->addMinutes($epreuve->duree_en_minute);
        $fin = $heureFinCalculee->format('H:i:s');
      } else {
        $fin = $heureFin;
      }

      // Créer le planning
      DB::table('planning_epreuves')->insert([
        'id' => Str::uuid(),
        'epreuve_id' => $epreuveId,
        'concours_id' => $concoursId,
        'session_id' => $session->id,
        'date_epreuve' => $date,
        'heure_debut' => $debut,
        'heure_fin' => $fin,
        'created_at' => now(),
        'updated_at' => now(),
      ]);

      return $epreuve->fresh('plannings');
    }, "EpreuveService::attachEpreuveToConcours");
  }

  /**
   * Détacher une épreuve d'un concours (supprimer le planning).
   */
  public function detachEpreuveFromConcours(string $concoursId, string $epreuveId): bool
  {
    return runTransaction(function () use ($concoursId, $epreuveId) {
      // Vérifier qu'il n'y a pas de notes saisies
      $hasNotes = Note::whereHas('candidature', function ($query) use ($concoursId) {
        $query->where('concours_id', $concoursId);
      })->where('epreuve_id', $epreuveId)->exists();

      if ($hasNotes) {
        throw new \Exception('Impossible de détacher cette épreuve car des notes ont déjà été saisies');
      }

      // Supprimer le(s) planning(s)
      $deleted = DB::table('planning_epreuves')
        ->where('concours_id', $concoursId)
        ->where('epreuve_id', $epreuveId)
        ->delete();

      if (!$deleted) {
        throw new \Exception('Cette épreuve n\'est pas planifiée pour ce concours');
      }

      return true;
    }, "EpreuveService::detachEpreuveFromConcours");
  }

  /**
   * Mettre à jour les paramètres d'un planning d'épreuve.
   */
  public function updateEpreuveParams(string $concoursId, string $epreuveId, array $params): Epreuve
  {
    return runTransaction(function () use ($concoursId, $epreuveId, $params) {
      // Mettre à jour le planning
      $updated = DB::table('planning_epreuves')
        ->where('concours_id', $concoursId)
        ->where('epreuve_id', $epreuveId)
        ->update(array_merge(
          array_filter($params, fn($key) => in_array($key, ['date_epreuve', 'heure_debut', 'heure_fin']), ARRAY_FILTER_USE_KEY),
          ['updated_at' => now()]
        ));

      if (!$updated) {
        throw new \Exception('Cette épreuve n\'est pas attachée à ce concours');
      }

      return Epreuve::findOrFail($epreuveId);
    }, "EpreuveService::updateEpreuveParams");
  }
}
