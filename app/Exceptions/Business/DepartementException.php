<?php

namespace App\Exceptions\Business;

use Exception;

/**
 * Exception personnalisée pour les erreurs liées aux départements.
 */
class DepartementException extends Exception
{
  /**
   * Département non trouvé.
   */
  public static function notFound(string $id): self
  {
    return new self("Département avec l'ID {$id} introuvable.", 404);
  }

  /**
   * Département déjà existant.
   *
   * @deprecated Utiliser alreadyExistsInEcole() à la place
   */
  public static function alreadyExists(string $code): self
  {
    return new self("Un département avec le code '{$code}' existe déjà.", 400);
  }

  /**
   * Département déjà existant dans une école.
   */
  public static function alreadyExistsInEcole(string $code, string $ecoleId): self
  {
    return new self("Un département avec le code '{$code}' existe déjà dans cette école.", 400);
  }

  /**
   * Échec de création.
   */
  public static function creationFailed(string $reason): self
  {
    return new self("Échec de création du département: {$reason}", 500);
  }

  /**
   * Échec de mise à jour.
   */
  public static function updateFailed(string $id, string $reason): self
  {
    return new self("Échec de mise à jour du département {$id}: {$reason}", 500);
  }

  /**
   * Échec de suppression.
   */
  public static function deleteFailed(string $id, string $reason): self
  {
    return new self("Échec de suppression du département {$id}: {$reason}", 500);
  }

  /**
   * Département déjà actif.
   */
  public static function alreadyActive(string $id): self
  {
    return new self("Le département {$id} est déjà actif.", 400);
  }

  /**
   * Département déjà inactif.
   */
  public static function alreadyInactive(string $id): self
  {
    return new self("Le département {$id} est déjà inactif.", 400);
  }

  /**
   * Impossible de désactiver le département.
   */
  public static function cannotDeactivate(string $id, string $reason): self
  {
    return new self("Impossible de désactiver le département {$id}: {$reason}.", 400);
  }

  /**
   * Département a des dépendances.
   */
  public static function hasDependencies(string $id, string $type): self
  {
    return new self("Impossible de supprimer le département {$id}: il possède des {$type}.", 400);
  }
}
