<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait HasServiceCache
{
  /**
   * Récupérer ou mettre en cache
   */
  protected function cacheRemember(string $key, int $ttl, callable $callback): mixed
  {
    return Cache::remember($key, $ttl, $callback);
  }

  /**
   * Invalider le cache
   */
  protected function cacheForget(string $key): void
  {
    Cache::forget($key);
  }

  /**
   * Invalider plusieurs clés de cache
   */
  protected function cacheForgetMany(array $keys): void
  {
    foreach ($keys as $key) {
      Cache::forget($key);
    }
  }

  /**
   * Invalider le cache par pattern (nécessite Redis)
   */
  protected function cacheForgetPattern(string $pattern): void
  {
    if (config('cache.default') === 'redis') {
      $keys = Cache::getRedis()->keys($pattern);
      if (!empty($keys)) {
        Cache::getRedis()->del($keys);
      }
    }
  }

  /**
   * Générer une clé de cache
   */
  protected function cacheKey(string ...$parts): string
  {
    return implode('.', array_filter($parts));
  }
}
