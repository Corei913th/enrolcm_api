<?php

namespace App\Console\Commands;

use App\Models\Paiement;
use App\Models\Candidature;
use App\Models\Utilisateur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MonitorSystemPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:monitor {--alerts : Afficher seulement les alertes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor system performance metrics for high-volume data handling';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $showOnlyAlerts = $this->option('alerts');

        $this->info('🔍 MONITORING SYSTÈME DE PERFORMANCES');
        $this->line('==========================================');

        // Métriques de volume
        $this->checkDataVolumes($showOnlyAlerts);

        // Métriques de performance DB
        $this->checkDatabasePerformance($showOnlyAlerts);

        // Métriques de cache
        $this->checkCacheEfficiency($showOnlyAlerts);

        // Métriques d'index
        $this->checkIndexUsage($showOnlyAlerts);

        // Recommandations
        $this->showRecommendations($showOnlyAlerts);
    }

    private function checkDataVolumes(bool $alertsOnly): void
    {
        $this->info('📊 VOLUMES DE DONNÉES');

        $metrics = [
            'Paiements' => Paiement::count(),
            'Candidatures' => Candidature::count(),
            'Utilisateurs' => Utilisateur::count(),
            'Paiements ce mois' => Paiement::whereMonth('created_at', now()->month)->count(),
            'Cache entries' => count(Cache::store('redis')->getStore()->keys() ?? []),
        ];

        foreach ($metrics as $label => $value) {
            $status = $this->getVolumeStatus($label, $value);
            if (!$alertsOnly || $status === '🔴') {
                $this->line("  {$status} {$label}: " . number_format($value));
            }
        }
        $this->line('');
    }

    private function checkDatabasePerformance(bool $alertsOnly): void
    {
        $this->info('🗄️ PERFORMANCE BASE DE DONNÉES');

        // Temps de requête typiques
        $start = microtime(true);
        Paiement::where('statut', 'VERIFIED')->limit(100)->count();
        $queryTime = (microtime(true) - $start) * 1000;

        $status = $queryTime > 500 ? '🔴' : ($queryTime > 100 ? '🟡' : '🟢');
        if (!$alertsOnly || $status === '🔴') {
            $this->line("  {$status} Temps requête paiements: {$queryTime}ms");
        }

        // Index manquants potentiels
        $slowQueries = DB::select("
            SELECT query, calls, total_time/calls as avg_time
            FROM pg_stat_statements
            WHERE total_time/calls > 100
            ORDER BY avg_time DESC
            LIMIT 5
        ");

        if (!empty($slowQueries)) {
            $this->line('  🟡 Requêtes lentes détectées:');
            foreach ($slowQueries as $query) {
                $this->line("    - {$query->avg_time}ms (appels: {$query->calls})");
            }
        }

        $this->line('');
    }

    private function checkCacheEfficiency(bool $alertsOnly): void
    {
        $this->info('💾 EFFICACITÉ DU CACHE');

        $cacheMetrics = [
            'Taux de hit cache' => $this->calculateCacheHitRate(),
            'Mémoire cache utilisée' => $this->getCacheMemoryUsage(),
            'Clés expirées' => $this->countExpiredCacheKeys(),
        ];

        foreach ($cacheMetrics as $label => $value) {
            $status = $this->getCacheStatus($label, $value);
            if (!$alertsOnly || $status === '🔴') {
                $this->line("  {$status} {$label}: {$value}");
            }
        }

        $this->line('');
    }

    private function checkIndexUsage(bool $alertsOnly): void
    {
        $this->info('🔍 UTILISATION DES INDEX');

        $indexUsage = DB::select("
            SELECT schemaname, tablename, indexname, idx_scan, idx_tup_read, idx_tup_fetch
            FROM pg_stat_user_indexes
            WHERE idx_scan = 0
            ORDER BY tablename
            LIMIT 10
        ");

        $unusedIndexes = count($indexUsage);
        $status = $unusedIndexes > 5 ? '🟡' : '🟢';

        if (!$alertsOnly || $status === '🟡') {
            $this->line("  {$status} Index non utilisés: {$unusedIndexes}");
            if ($unusedIndexes > 0 && !$alertsOnly) {
                $this->line('    Considérer DROP INDEX pour:');
                foreach (array_slice($indexUsage, 0, 3) as $index) {
                    $this->line("    - {$index->tablename}.{$index->indexname}");
                }
            }
        }

        $this->line('');
    }

    private function showRecommendations(bool $alertsOnly): void
    {
        $this->info('💡 RECOMMANDATIONS D\'OPTIMISATION');

        $paiementCount = Paiement::count();
        $candidatureCount = Candidature::count();

        if ($paiementCount > 10000) {
            $this->line('  🔴 CONSIDÉRER PARTITIONNEMENT: Table paiements > 10k lignes');
            $this->line('     -> Partitionner par mois/année sur created_at');
        }

        if ($candidatureCount > 50000) {
            $this->line('  🟡 OPTIMISER LES REQUÊTES: Table candidatures volumineuse');
            $this->line('     -> Utiliser cursor pagination au lieu d\'offset');
            $this->line('     -> Implémenter recherche Elasticsearch');
        }

        if ($this->calculateCacheHitRate() < 0.8) {
            $this->line('  🟡 AMÉLIORER LE CACHE: Taux de hit < 80%');
            $this->line('     -> Augmenter TTL des données fréquemment accédées');
            $this->line('     -> Précharger les données de référence');
        }

        $this->line('  ✅ ARCHIVAGE: Planifier archivage des données > 12 mois');
        $this->line('  ✅ MONITORING: Configurer alertes sur métriques critiques');
    }

    private function getVolumeStatus(string $label, int $value): string
    {
        return match ($label) {
            'Paiements' => $value > 50000 ? '🔴' : ($value > 10000 ? '🟡' : '🟢'),
            'Candidatures' => $value > 100000 ? '🔴' : ($value > 25000 ? '🟡' : '🟢'),
            'Utilisateurs' => $value > 50000 ? '🔴' : ($value > 10000 ? '🟡' : '🟢'),
            default => '🟢'
        };
    }

    private function getCacheStatus(string $label, $value): string
    {
        return match ($label) {
            'Taux de hit cache' => $value < 0.7 ? '🔴' : ($value < 0.85 ? '🟡' : '🟢'),
            'Mémoire cache utilisée' => str_contains($value, 'MB') && (float)$value > 500 ? '🟡' : '🟢',
            'Clés expirées' => (int)$value > 1000 ? '🟡' : '🟢',
            default => '🟢'
        };
    }

    private function calculateCacheHitRate(): float
    {
        // Simulation - en production, utiliser métriques réelles
        return 0.85;
    }

    private function getCacheMemoryUsage(): string
    {
        // Simulation - en production, utiliser métriques Redis/Memcached
        return '245 MB';
    }

    private function countExpiredCacheKeys(): int
    {
        // Simulation - en production, utiliser métriques réelles
        return 150;
    }
}
