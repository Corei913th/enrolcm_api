<?php

namespace App\Services\Domain\Session;

use App\Models\Session;
use App\Traits\HasServiceCache;
use App\Traits\HasActivityLogger;
use App\Services\Infrastructure\Logger\ActivityLoggerService;

class SessionService
{
  use HasServiceCache, HasActivityLogger;

  private const CACHE_TTL = 1800; // 30 minutes

  public function __construct(ActivityLoggerService $logger)
  {
    $this->logger = $logger;
  }

  /**
   * Obtenir la session active pour un concours
   * @param string $concoursId
   * @return Session 
   */
  public function getActiveSession(string $concoursId): ?Session
  {
    return $this->cacheRemember(
      $this->cacheKey('session', 'active', 'concours', $concoursId),
      self::CACHE_TTL,
      fn() => Session::where('est_actif', true)
        ->whereHas('concours', fn($query) => $query->where('concours.id', $concoursId))
        ->first()
    );
  }

  /**
   * Vérifier si une session est active pour un concours
   */
  public function hasActiveSession(string $concoursId): bool
  {
    return $this->getActiveSession($concoursId) !== null;
  }

  /**
   * Invalider le cache d'une session
   */
  public function invalidateCache(string $concoursId): void
  {
    $this->cacheForget($this->cacheKey('session', 'active', 'concours', $concoursId));
  }
}
