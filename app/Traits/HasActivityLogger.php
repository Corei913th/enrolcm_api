<?php

namespace App\Traits;

use App\Services\Infrastructure\Logger\ActivityLoggerService;


trait HasActivityLogger
{
  protected ActivityLoggerService $logger;

  /**
   * Logger une création d'entité
   */
  protected function logCreate(string $entity, string $entityId, ?array $data = null): void
  {
    $this->logger->logActivity('create', $entity, $entityId, $data);
  }

  /**
   * Logger une mise à jour d'entité
   */
  protected function logUpdate(string $entity, string $entityId, ?array $data = null): void
  {
    $this->logger->logActivity('update', $entity, $entityId, $data);
  }

  /**
   * Logger une suppression d'entité
   */
  protected function logDelete(string $entity, string $entityId): void
  {
    $this->logger->logActivity('delete', $entity, $entityId);
  }

  /**
   * Logger une consultation d'entité
   */
  protected function logView(string $entity, string $entityId): void
  {
    $this->logger->logActivity('view', $entity, $entityId);
  }

  /**
   * Logger une activation/désactivation
   */
  protected function logStatusChange(string $entity, string $entityId, bool $newStatus): void
  {
    $action = $newStatus ? 'activate' : 'deactivate';
    $this->logger->logActivity($action, $entity, $entityId, ['status' => $newStatus]);
  }

  /**
   * Logger une opération personnalisée
   */
  protected function logOperation(string $action, string $entity, ?string $entityId = null, ?array $data = null): void
  {
    $this->logger->logActivity($action, $entity, $entityId, $data);
  }
}
