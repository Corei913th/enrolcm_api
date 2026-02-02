<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;


trait HasServiceFinders
{
  /**
   * Récupérer une entité par ID
   * 
   * @param string $id
   * @param array $relations Relations à charger
   * @return Model
   * @throws \Exception
   */
  public function getById(string $id, array $relations = []): Model
  {
    try {
      $query = $this->getModelClass()::query();

      if (!empty($relations)) {
        $query->with($relations);
      }

      return $query->findOrFail($id);
    } catch (ModelNotFoundException $e) {
      $this->throwNotFoundException($id);
    }
  }

  /**
   * Récupérer une entité par une colonne spécifique
   * 
   * @param string $column
   * @param mixed $value
   * @param array $relations
   * @return Model
   * @throws \Exception
   */
  public function getBy(string $column, mixed $value, array $relations = []): Model
  {
    try {
      $query = $this->getModelClass()::query();

      if (!empty($relations)) {
        $query->with($relations);
      }

      return $query->where($column, $value)->firstOrFail();
    } catch (ModelNotFoundException $e) {
      $this->throwNotFoundException($value, $column);
    }
  }

  /**
   * Récupérer une entité par code
   * 
   * @param string $code
   * @param array $relations
   * @return Model
   * @throws \Exception
   */
  public function getByCode(string $code, array $relations = []): Model
  {
    $codeColumn = $this->getCodeColumn();
    return $this->getBy($codeColumn, $code, $relations);
  }

  /**
   * Trouver une entité par colonne (retourne null si non trouvé)
   * 
   * @param string $column
   * @param mixed $value
   * @param array $relations
   * @return Model|null
   */
  public function findBy(string $column, mixed $value, array $relations = []): ?Model
  {
    $query = $this->getModelClass()::query();

    if (!empty($relations)) {
      $query->with($relations);
    }

    return $query->where($column, $value)->first();
  }

  /**
   * Trouver une entité par code (retourne null si non trouvé)
   * 
   * @param string $code
   * @param array $relations
   * @return Model|null
   */
  public function findByCode(string $code, array $relations = []): ?Model
  {
    $codeColumn = $this->getCodeColumn();
    return $this->findBy($codeColumn, $code, $relations);
  }

  /**
   * Vérifier si une entité existe par colonne
   * 
   * @param string $column
   * @param mixed $value
   * @return bool
   */
  public function existsBy(string $column, mixed $value): bool
  {
    return $this->getModelClass()::where($column, $value)->exists();
  }

  /**
   * Vérifier si une entité existe par code
   * 
   * @param string $code
   * @return bool
   */
  public function existsByCode(string $code): bool
  {
    $codeColumn = $this->getCodeColumn();
    return $this->existsBy($codeColumn, $code);
  }

  /**
   * Récupérer plusieurs entités par colonne
   * 
   * @param string $column
   * @param mixed $value
   * @param array $relations
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getManyBy(string $column, mixed $value, array $relations = [])
  {
    $query = $this->getModelClass()::query();

    if (!empty($relations)) {
      $query->with($relations);
    }

    return $query->where($column, $value)->get();
  }

  /**
   * Obtenir la classe du modèle
   * À définir dans le service ou via propriété $modelClass
   * 
   * @return string
   */
  protected function getModelClass(): string
  {
    if (property_exists($this, 'modelClass')) {
      return $this->modelClass;
    }

    // Déduire automatiquement depuis le nom du service
    // Ex: EcoleService -> App\Models\Ecole
    $serviceName = class_basename(static::class);
    $modelName = str_replace('Service', '', $serviceName);

    return "App\\Models\\{$modelName}";
  }

  /**
   * Obtenir le nom de la colonne code
   * À surcharger dans le service si différent
   * 
   * @return string
   */
  protected function getCodeColumn(): string
  {
    if (property_exists($this, 'codeColumn')) {
      return $this->codeColumn;
    }

    // Par défaut: code_{model_name}
    $modelName = class_basename($this->getModelClass());
    return 'code_' . strtolower($modelName);
  }

  /**
   * Lancer une exception de type "not found"
   * 
   * @param mixed $value
   * @param string $column
   * @throws \Exception
   */
  protected function throwNotFoundException(mixed $value, string $column = 'id'): void
  {
    $modelName = class_basename($this->getModelClass());

    // Si une classe d'exception personnalisée est définie
    if (property_exists($this, 'exceptionClass') && class_exists($this->exceptionClass)) {
      $exceptionClass = $this->exceptionClass;

      // Essayer d'appeler une méthode statique notFound() si elle existe
      if (method_exists($exceptionClass, 'notFound')) {
        throw $exceptionClass::notFound($value);
      }

      throw new $exceptionClass("{$modelName} non trouvé: {$column} = {$value}");
    }

    // Exception générique
    throw new \Exception("{$modelName} non trouvé: {$column} = {$value}", 404);
  }
}
