<?php

namespace App\Services\Departements;

use App\Models\Departement;
use App\DTOs\Departements\CreateDepartementDTO;
use App\DTOs\Departements\UpdateDepartementDTO;
use App\Exceptions\Business\DepartementException;
use App\Models\Ecole;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class DepartementService
{
  /**
   * Créer un nouveau département.
   *
   * @param CreateDepartementDTO $dto Données du département
   *
   * @return Departement Département créé
   *
   * @throws DepartementException Si le département existe déjà dans cette école
   */
  public function create(CreateDepartementDTO $dto): Departement
  {
    // Vérifier si le département existe déjà dans cette école
    if ($this->existsByEcoleAndCode($dto->ecole_id, $dto->code_departement)) {
      throw DepartementException::alreadyExistsInEcole($dto->code_departement, $dto->ecole_id);
    }

    // Générer le code composite unique (école + département)
    $codeComposite = $this->genererCodeComposite($dto->ecole_id, $dto->code_departement);

    try {
      return Departement::create([
        'code_departement' => $codeComposite,
        'libelle_departement' => $dto->libelle_departement,
        'ecole_id' => $dto->ecole_id,
        'desc_departement' => $dto->desc_departement,
        'est_actif' => $dto->est_actif ?? true,
      ]);
    } catch (\Exception $e) {
      throw DepartementException::creationFailed($e->getMessage());
    }
  }

  /**
   * Mettre à jour un département.
   *
   * @param string $id ID du département
   * @param UpdateDepartementDTO $dto Données à mettre à jour
   *
   * @return Departement Département mis à jour
   *
   * @throws DepartementException Si le département n'existe pas
   */
  public function update(string $id, UpdateDepartementDTO $dto): Departement
  {
    $departement = $this->findById($id);


    if ($dto->code_departement && $dto->code_departement !== $this->extraireCodeDepartement($departement->code_departement)) {
      $ecoleId = $dto->ecole_id ?? $departement->ecole_id;
      if ($this->existsByEcoleAndCode($ecoleId, $dto->code_departement)) {
        throw DepartementException::alreadyExistsInEcole($dto->code_departement, $ecoleId);
      }
    }

    $nouveauCodeDepartement = $dto->code_departement ?? $this->extraireCodeDepartement($departement->code_departement);
    $nouvelEcoleId = $dto->ecole_id ?? $departement->ecole_id;


    $nouveauCodeComposite = $this->genererCodeComposite($nouvelEcoleId, $nouveauCodeDepartement);

    try {
      $departement->update([
        'code_departement' => $nouveauCodeComposite,
        'libelle_departement' => $dto->libelle_departement ?? $departement->libelle_departement,
        'ecole_id' => $nouvelEcoleId,
        'desc_departement' => $dto->desc_departement ?? $departement->desc_departement,
        'est_actif' => $dto->est_actif ?? $departement->est_actif,
      ]);

      return $departement->fresh();
    } catch (\Exception $e) {
      throw DepartementException::updateFailed($id, $e->getMessage());
    }
  }

  /**
   * Supprimer un département.
   *
   * @param string $id ID du département
   *
   * @return bool True si supprimé
   *
   * @throws DepartementException Si le département a des dépendances
   */
  public function delete(string $id): bool
  {
    $departement = $this->findById($id);


    if ($departement->centres()->exists()) {
      throw DepartementException::hasDependencies($id, 'centres');
    }

    if ($departement->filieres()->exists()) {
      throw DepartementException::hasDependencies($id, 'filières');
    }

    try {
      return $departement->delete();
    } catch (\Exception $e) {
      throw DepartementException::deleteFailed($id, $e->getMessage());
    }
  }

  /**
   * Activer un département.
   *
   * @param string $id ID du département
   *
   * @return Departement Département activé
   */
  public function activate(string $id): Departement
  {
    $departement = $this->findById($id);

    if ($departement->est_actif) {
      throw DepartementException::alreadyActive($id);
    }

    $departement->update(['est_actif' => true]);
    return $departement->fresh();
  }

  /**
   * Désactiver un département.
   *
   * @param string $id ID du département
   *
   * @return Departement Département désactivé
   */
  public function deactivate(string $id): Departement
  {
    $departement = $this->findById($id);

    if (!$departement->est_actif) {
      throw DepartementException::alreadyInactive($id);
    }

    // Vérifier qu'aucune filière active n'est liée
    if ($departement->filieres()->where('est_actif', true)->exists()) {
      throw DepartementException::cannotDeactivate($id, 'filières actives');
    }

    $departement->update(['est_actif' => false]);
    return $departement->fresh();
  }

  /**
   * Trouver un département par ID.
   *
   * @param string $id ID du département
   *
   * @return Departement Département trouvé
   *
   * @throws DepartementException Si non trouvé
   */
  public function findById(string $id): Departement
  {
    try {
      return Departement::findOrFail($id);
    } catch (ModelNotFoundException) {
      throw DepartementException::notFound($id);
    }
  }

  /**
   * Trouver un département par code.
   *
   * @param string $code Code du département
   *
   * @return Departement|null Département trouvé ou null
   */
  public function findByCode(string $code): ?Departement
  {
    return Departement::where('code_departement', $code)->first();
  }

  /**
   * Vérifier si un département existe par code dans une école.
   *
   * @param string $ecoleId ID de l'école
   * @param string $code Code du département
   *
   * @return bool True si existe
   */
  public function existsByEcoleAndCode(string $ecoleId, string $code): bool
  {
    return Departement::where('ecole_id', $ecoleId)
      ->where('code_departement', $code)
      ->exists();
  }

  /**
   * Générer le code composite unique (code école - code département).
   *
   * @param string $ecoleId ID de l'école
   * @param string $codeDepartement Code du département
   *
   * @return string Code composite unique
   */
  private function genererCodeComposite(string $ecoleId, string $codeDepartement): string
  {
    // Récupérer le vrai code de l'école depuis la base
    $ecole = Ecole::find($ecoleId);
    $codeEcole = $ecole ? $ecole->code_ecole : $ecoleId;

    return $codeEcole . '-' . $codeDepartement;
  }

  /**
   * Extraire le code département depuis le code composite.
   * Format: CODEECOLE-CODEDEPARTEMENT
   *
   * @param string $codeComposite Code composite stocké
   *
   * @return string Code du département seul
   */
  private function extraireCodeDepartement(string $codeComposite): string
  {
    $parts = explode('-', $codeComposite, 2);
    return $parts[1] ?? $codeComposite;
  }

  /**
   * Extraire le code de l'école depuis le code composite.
   *
   * @param string $codeComposite Code composite stocké
   *
   * @return string Code de l'école
   */
  private function extraireCodeEcole(string $codeComposite): string
  {
    $parts = explode('-', $codeComposite, 2);
    return $parts[0] ?? $codeComposite;
  }

  /**
   * Vérifier si un département existe par code globalement (pour compatibilité).
   *
   * @param string $code Code du département
   *
   * @return bool True si existe
   *
   * @deprecated Utiliser existsByEcoleAndCode() à la place
   */
  public function existsByCode(string $code): bool
  {
    return Departement::where('code_departement', $code)->exists();
  }

  /**
   * Lister tous les départements.
   *
   * @param array $filters Filtres optionnels
   *
   * @return Collection Liste des départements
   */
  public function getAll(array $filters = []): Collection
  {
    $query = Departement::with(['ecole', 'filieres']);

    // Appliquer les filtres
    if (isset($filters['est_actif'])) {
      $query->where('est_actif', $filters['est_actif']);
    }

    if (isset($filters['ecole_id'])) {
      $query->where('ecole_id', $filters['ecole_id']);
    }

    if (isset($filters['search'])) {
      $search = $filters['search'];
      $query->where(function ($q) use ($search) {
        $q->where('code_departement', 'like', "%{$search}%")
          ->orWhere('libelle_departement', 'like', "%{$search}%");
      });
    }

    return $query->orderBy('code_departement')->get();
  }

  /**
   * Obtenir les statistiques d'un département.
   *
   * @param string $id ID du département
   *
   * @return array Statistiques du département
   */
  public function getStats(string $id): array
  {
    $departement = $this->findById($id);

    return [
      'total_filieres' => $departement->filieres()->count(),
      'filieres_actives' => $departement->filieres()->where('est_actif', true)->count(),
    ];
  }
}
