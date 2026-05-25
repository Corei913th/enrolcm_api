<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait HasSmartCache
{
    /**
     * Durée de cache par défaut (en secondes).
     */
    protected int $defaultCacheDuration = 300; // 5 minutes

    /**
     * Durée de cache pour les listes (en secondes).
     */
    protected int $listCacheDuration = 180; // 3 minutes

    /**
     * Durée de cache pour les détails (en secondes).
     */
    protected int $detailCacheDuration = 600; // 10 minutes

    /**
     * Durée de cache pour les données statiques (en secondes).
     */
    protected int $staticCacheDuration = 3600; // 1 heure

    /**
     * Récupère ou met en cache le résultat d'une opération.
     *
     * @param  string  $key  Clé de cache
     * @param  callable  $callback  Fonction à exécuter si cache manquant
     * @param  int|null  $duration  Durée de cache (null = défaut)
     * @param  array  $tags  Tags pour invalidation groupée
     * @return mixed Résultat
     */
    protected function remember(string $key, callable $callback, ?int $duration = null, array $tags = [])
    {
        $duration = $duration ?? $this->defaultCacheDuration;
        $key = $this->formatCacheKey($key);

        // Vérifier si le driver supporte les tags
        if (! empty($tags) && $this->cacheSupportsTagging()) {
            return Cache::tags($tags)->remember($key, $duration, $callback);
        }

        return Cache::remember($key, $duration, $callback);
    }

    /**
     * Cache une liste paginée avec ses filtres.
     *
     * @param  array  $filters  Filtres de recherche
     * @param  int  $page  Page actuelle
     * @param  int  $perPage  Éléments par page
     * @param  callable  $callback  Fonction à exécuter
     * @param  string  $prefix  Préfixe de la clé
     * @return mixed Résultat
     */
    protected function rememberList(array $filters, int $page, int $perPage, callable $callback, string $prefix = 'list')
    {
        $key = $this->generateListCacheKey($prefix, $filters, $page, $perPage);
        $tags = $this->getModelTags();

        return $this->remember($key, $callback, $this->listCacheDuration, $tags);
    }

    /**
     * Cache les détails d'une entité.
     *
     * @param  string  $id  ID de l'entité
     * @param  callable  $callback  Fonction à exécuter
     * @param  string  $prefix  Préfixe de la clé
     * @return mixed Résultat
     */
    protected function rememberDetail(string $id, callable $callback, string $prefix = 'detail')
    {
        $key = "{$prefix}:{$id}";
        $tags = $this->getModelTags();

        return $this->remember($key, $callback, $this->detailCacheDuration, $tags);
    }

    /**
     * Cache des données statiques (rarement modifiées).
     *
     * @param  string  $key  Clé de cache
     * @param  callable  $callback  Fonction à exécuter
     * @return mixed Résultat
     */
    protected function rememberStatic(string $key, callable $callback)
    {
        $tags = $this->getModelTags();

        return $this->remember($key, $callback, $this->staticCacheDuration, $tags);
    }

    /**
     * Invalide le cache d'une entité spécifique.
     *
     * @param  string  $id  ID de l'entité
     * @param  string  $prefix  Préfixe de la clé
     */
    protected function forgetDetail(string $id, string $prefix = 'detail'): void
    {
        $key = $this->formatCacheKey("{$prefix}:{$id}");
        Cache::forget($key);
    }

    /**
     * Invalide tout le cache d'un modèle.
     */
    protected function flushModelCache(): void
    {
        $tags = $this->getModelTags();

        if (! empty($tags) && $this->cacheSupportsTagging()) {
            Cache::tags($tags)->flush();
        } else {
            // Fallback: vider tout le cache si tags non supportés
            Cache::flush();
        }
    }

    /**
     * Invalide le cache des listes uniquement.
     */
    protected function flushListCache(): void
    {
        $tags = array_merge($this->getModelTags(), ['lists']);

        if (! empty($tags) && $this->cacheSupportsTagging()) {
            Cache::tags($tags)->flush();
        } else {
            // Fallback: vider tout le cache si tags non supportés
            Cache::flush();
        }
    }

    /**
     * Génère une clé de cache pour une liste avec filtres.
     *
     * @param  string  $prefix  Préfixe
     * @param  array  $filters  Filtres
     * @param  int  $page  Page
     * @param  int  $perPage  Éléments par page
     * @return string Clé de cache
     */
    private function generateListCacheKey(string $prefix, array $filters, int $page, int $perPage): string
    {
        // Trier les filtres pour avoir une clé cohérente
        ksort($filters);

        $filterHash = md5(json_encode($filters));

        return "{$prefix}:{$filterHash}:p{$page}:pp{$perPage}";
    }

    /**
     * Formate une clé de cache avec le nom du service.
     *
     * @param  string  $key  Clé brute
     * @return string Clé formatée
     */
    private function formatCacheKey(string $key): string
    {
        $serviceName = strtolower(class_basename(static::class));

        return "{$serviceName}:{$key}";
    }

    /**
     * Retourne les tags de cache pour le modèle.
     * À surcharger dans les services pour définir des tags spécifiques.
     *
     * @return array Tags
     */
    protected function getModelTags(): array
    {
        $serviceName = strtolower(str_replace('Service', '', class_basename(static::class)));

        return [$serviceName];
    }

    /**
     * Vérifie si le cache est activé.
     */
    protected function isCacheEnabled(): bool
    {
        return config('cache.enabled', true) && ! app()->environment('testing');
    }

    /**
     * Exécute avec ou sans cache selon la configuration.
     *
     * @param  string  $key  Clé de cache
     * @param  callable  $callback  Fonction à exécuter
     * @param  int|null  $duration  Durée de cache
     * @param  array  $tags  Tags
     * @return mixed Résultat
     */
    protected function cacheOrExecute(string $key, callable $callback, ?int $duration = null, array $tags = [])
    {
        if (! $this->isCacheEnabled()) {
            return $callback();
        }

        return $this->remember($key, $callback, $duration, $tags);
    }

    /**
     * Invalide le cache après une opération de modification.
     *
     * @param  string|null  $id  ID de l'entité modifiée (null pour flush complet)
     */
    protected function invalidateCacheAfterModification(?string $id = null): void
    {
        if ($id) {
            // Invalider le détail spécifique
            $this->forgetDetail($id);
        }

        // Toujours invalider les listes car elles peuvent être affectées
        $this->flushListCache();
    }

    /**
     * Préchauffe le cache avec des données fréquemment utilisées.
     *
     * @param  array  $items  Items à mettre en cache
     * @param  string  $prefix  Préfixe de clé
     */
    protected function warmupCache(array $items, string $prefix = 'detail'): void
    {
        foreach ($items as $item) {
            if (isset($item['id'])) {
                $key = $this->formatCacheKey("{$prefix}:{$item['id']}");
                $tags = $this->getModelTags();

                if (! empty($tags) && $this->cacheSupportsTagging()) {
                    Cache::tags($tags)->put($key, $item, $this->detailCacheDuration);
                } else {
                    Cache::put($key, $item, $this->detailCacheDuration);
                }
            }
        }
    }

    /**
     * Vérifie si le driver de cache supporte le tagging.
     */
    protected function cacheSupportsTagging(): bool
    {
        $driver = config('cache.default');
        $supportedDrivers = ['redis', 'memcached', 'octane'];

        return in_array($driver, $supportedDrivers);
    }
}
