<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasAdvancedSearch
{
  /**
   * Applique une recherche optimisée sur plusieurs colonnes.
   * Gère les espaces multiples, trim automatique, et recherche partielle.
   *
   * @param Builder $query
   * @param string|null $search
   * @param array $columns Colonnes à rechercher ['column' => 'type'] où type = 'exact', 'partial', 'start', 'end'
   * @param array $relations Relations à rechercher ['relation.column' => 'type']
   * @param bool $caseSensitive Si true, utilise LIKE (sensible à la casse), sinon ILIKE (insensible, par défaut)
   * @return Builder
   */
  protected function applySearch(Builder $query, ?string $search, array $columns = [], array $relations = [], bool $caseSensitive = false): Builder
  {
    if (empty($search)) {
      return $query;
    }

    // Nettoyer la recherche : trim + espaces multiples
    $search = trim(preg_replace('/\s+/', ' ', $search));

    if (empty($search)) {
      return $query;
    }

    return $query->where(function (Builder $q) use ($search, $columns, $relations, $caseSensitive) {
      // Recherche dans les colonnes directes
      foreach ($columns as $column => $type) {
        $this->addSearchCondition($q, $column, $search, $type, true, $caseSensitive);
      }

      // Recherche dans les relations
      foreach ($relations as $relationColumn => $type) {
        [$relation, $column] = explode('.', $relationColumn, 2);

        $q->orWhereHas($relation, function (Builder $subQuery) use ($column, $search, $type, $caseSensitive) {
          $this->addSearchCondition($subQuery, $column, $search, $type, false, $caseSensitive);
        });
      }
    });
  }

  /**
   * Ajoute une condition de recherche selon le type.
   *
   * @param Builder $query
   * @param string $column
   * @param string $search
   * @param string $type 'exact', 'partial', 'start', 'end', 'words'
   * @param bool $useOr
   * @param bool $caseSensitive Si true, utilise LIKE (sensible à la casse), sinon ILIKE (insensible, par défaut)
   * @return void
   */
  private function addSearchCondition(Builder $query, string $column, string $search, string $type, bool $useOr, bool $caseSensitive = false): void
  {
    $method = $useOr ? 'orWhere' : 'where';
    $operator = $caseSensitive ? 'LIKE' : 'ILIKE';

    switch ($type) {
      case 'exact':
        // Recherche exacte
        $query->$method($column, $operator, $search);
        break;

      case 'start':
        // Commence par
        $query->$method($column, $operator, $search . '%');
        break;

      case 'end':
        // Termine par
        $query->$method($column, $operator, '%' . $search);
        break;

      case 'words':
        // Recherche par mots (chaque mot doit être présent)
        $words = explode(' ', $search);
        $query->$method(function (Builder $q) use ($column, $words, $operator) {
          foreach ($words as $word) {
            if (!empty($word)) {
              $q->where($column, $operator, '%' . $word . '%');
            }
          }
        });
        break;

      case 'partial':
      default:
        // Recherche partielle (contient)
        $query->$method($column, $operator, '%' . $search . '%');
        break;
    }
  }

  /**
   * Applique des filtres multiples de manière optimisée.
   *
   * @param Builder $query
   * @param array $filters ['column' => 'value'] ou ['column' => ['value1', 'value2']]
   * @return Builder
   */
  protected function applyFilters(Builder $query, array $filters): Builder
  {
    foreach ($filters as $column => $value) {
      if ($value === null || $value === '') {
        continue;
      }

      if (is_array($value)) {
        // Filtre IN pour les tableaux
        $query->whereIn($column, $value);
      } else {
        // Filtre égalité simple
        $query->where($column, $value);
      }
    }

    return $query;
  }

  /**
   * Applique un tri optimisé.
   *
   * @param Builder $query
   * @param string|null $sortBy
   * @param string $sortOrder 'asc' ou 'desc'
   * @param string $defaultSort Colonne de tri par défaut
   * @param array $allowedColumns Colonnes autorisées pour le tri
   * @return Builder
   */
  protected function applySort(Builder $query, ?string $sortBy, string $sortOrder = 'asc', string $defaultSort = 'created_at', array $allowedColumns = []): Builder
  {
    $sortBy = $sortBy ?: $defaultSort;
    $sortOrder = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';

    // Vérifier que la colonne est autorisée
    if (!empty($allowedColumns) && !in_array($sortBy, $allowedColumns)) {
      $sortBy = $defaultSort;
    }

    return $query->orderBy($sortBy, $sortOrder);
  }

  /**
   * Applique une recherche par date range.
   *
   * @param Builder $query
   * @param string $column
   * @param string|null $startDate
   * @param string|null $endDate
   * @return Builder
   */
  protected function applyDateRange(Builder $query, string $column, ?string $startDate, ?string $endDate): Builder
  {
    if ($startDate) {
      $query->whereDate($column, '>=', $startDate);
    }

    if ($endDate) {
      $query->whereDate($column, '<=', $endDate);
    }

    return $query;
  }

  /**
   * Optimise la requête en ajoutant des index hints si nécessaire.
   *
   * @param Builder $query
   * @param array $indexes
   * @return Builder
   */
  protected function optimizeQuery(Builder $query, array $indexes = []): Builder
  {
    // Pour PostgreSQL, on peut utiliser des hints via des extensions
    // Pour MySQL, on utiliserait USE INDEX
    // Ici on laisse l'optimiseur faire son travail avec les bons index

    return $query;
  }
}
