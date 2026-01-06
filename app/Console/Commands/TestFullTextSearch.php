<?php

namespace App\Console\Commands;

use App\Models\Paiement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestFullTextSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:test-performance {query?} {--iterations=5 : Nombre de tests} {--full : Test complet avec données}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test des performances de recherche full-text vs recherche traditionnelle';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = $this->argument('query') ?: 'RCP2026';
        $iterations = $this->option('iterations');
        $fullTest = $this->option('full');

        $this->info('🔍 TEST DE PERFORMANCE - RECHERCHE FULL-TEXT');
        $this->line('================================================');
        $this->info("Requête de test: '{$query}'");
        $this->info("Nombre d'itérations: {$iterations}");

        // Vérifier les données de test
        $totalPaiements = Paiement::count();
        $this->info("Total paiements en base: " . number_format($totalPaiements));

        if ($totalPaiements === 0) {
            $this->error('Aucun paiement en base ! Lancez les seeders d\'abord.');
            return;
        }

        // TEST 1: Recherche traditionnelle (LIKE)
        $this->info("\n📊 TEST 1: Recherche traditionnelle (LIKE)");
        $traditionalTimes = [];
        $traditionalResults = 0;

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);

            $results = Paiement::where('reference', 'like', "%{$query}%")
                ->orWhere('validation_notes', 'like', "%{$query}%")
                ->limit(50)
                ->get();

            $time = (microtime(true) - $start) * 1000;
            $traditionalTimes[] = $time;
            $traditionalResults = $results->count();

            if ($i === 0) {
                $this->line("  Résultats trouvés: {$results->count()}");
            }
        }

        $traditionalAvg = array_sum($traditionalTimes) / count($traditionalTimes);

        // TEST 2: Recherche full-text (GIN index)
        $this->info("\n🚀 TEST 2: Recherche full-text (GIN index)");
        $fulltextTimes = [];
        $fulltextResults = 0;

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);

            $results = Paiement::whereRaw("search_vector @@ plainto_tsquery('french', ?)", [$query])
                ->orWhere('reference', 'ilike', "%{$query}%")
                ->limit(50)
                ->get();

            $time = (microtime(true) - $start) * 1000;
            $fulltextTimes[] = $time;
            $fulltextResults = $results->count();

            if ($i === 0) {
                $this->line("  Résultats trouvés: {$results->count()}");
            }
        }

        $fulltextAvg = array_sum($fulltextTimes) / count($fulltextTimes);

        // RÉSULTATS
        $this->line("\n📈 RÉSULTATS DE PERFORMANCE");
        $this->line('=====================================');

        $this->table(
            ['Méthode', 'Temps moyen (ms)', 'Résultats', 'Amélioration'],
            [
                [
                    'Traditionnelle (LIKE)',
                    number_format($traditionalAvg, 2),
                    $traditionalResults,
                    'Baseline'
                ],
                [
                    'Full-text (GIN)',
                    number_format($fulltextAvg, 2),
                    $fulltextResults,
                    $traditionalAvg > 0 ? number_format(($traditionalAvg - $fulltextAvg) / $traditionalAvg * 100, 1) . '%' : 'N/A'
                ]
            ]
        );

        // ANALYSE DES INDEX
        if ($fullTest) {
            $this->info("\n🔍 ANALYSE DES INDEX ET OPTIMISATIONS");

            // Vérifier l'utilisation des index
            $indexUsage = DB::select("
                SELECT indexname, idx_scan, idx_tup_read, idx_tup_fetch
                FROM pg_stat_user_indexes
                WHERE tablename = 'paiements' AND indexname LIKE '%search%'
                ORDER BY idx_scan DESC
            ");

            if (!empty($indexUsage)) {
                $this->table(
                    ['Index', 'Scans', 'Tuples lus', 'Tuples retournés'],
                    collect($indexUsage)->map(fn($idx) => [
                        $idx->indexname,
                        number_format($idx->idx_scan),
                        number_format($idx->idx_tup_read),
                        number_format($idx->idx_tup_fetch)
                    ])->toArray()
                );
            }

            // Test de pertinence
            $this->info("\n🎯 TEST DE PERTINENCE (ranking)");
            $topResults = Paiement::whereRaw("search_vector @@ plainto_tsquery('french', ?)", [$query])
                ->select(['reference', 'validation_notes'])
                ->selectRaw("ts_rank(search_vector, plainto_tsquery('french', ?)) as rank", [$query])
                ->orderBy('rank', 'desc')
                ->limit(5)
                ->get();

            if ($topResults->isNotEmpty()) {
                $this->table(
                    ['Référence', 'Notes', 'Score de pertinence'],
                    $topResults->map(fn($p) => [
                        $p->reference,
                        substr($p->validation_notes ?: '', 0, 50) . '...',
                        number_format($p->rank, 3)
                    ])->toArray()
                );
            }
        }

        // RECOMMANDATIONS
        $this->info("\n💡 RECOMMANDATIONS");
        if ($fulltextAvg < $traditionalAvg) {
            $improvement = ($traditionalAvg - $fulltextAvg) / $traditionalAvg * 100;
            if ($improvement > 50) {
                $this->success("🎉 Excellent ! La recherche full-text est " . number_format($improvement, 0) . "% plus rapide !");
                $this->line("   → Utilisez ts_rank() pour le ranking par pertinence");
                $this->line("   → Index GIN parfait pour gros volumes");
            }
        } else {
            $this->warn("⚠️  La recherche full-text n'est pas plus rapide pour ce cas d'usage");
            $this->line("   → Considérez l'optimisation des index LIKE");
            $this->line("   → Ou utilisez full-text pour la recherche complexe seulement");
        }

        $this->line("   → Pour " . number_format($totalPaiements) . "+ paiements, full-text sera crucial");
        $this->line("   → Testez avec plus de données pour voir l'impact à échelle");
    }
}
