<?php

namespace App\Console\Commands;

use App\Models\Paiement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArchiveOldPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:archive {--months=12 : Nombre de mois avant archivage} {--dry-run : Aperçu sans archivage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archiver les anciens paiements pour optimiser les performances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = $this->option('months');
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subMonths($months);

        $this->info("Archivage des paiements antérieurs au {$cutoffDate->format('Y-m-d')}");
        $this->info('Mode dry-run: ' . ($dryRun ? 'OUI' : 'NON'));

        // Compter les paiements à archiver
        $count = Paiement::where('created_at', '<', $cutoffDate)
            ->whereIn('statut', ['VERIFIED', 'REJECTED']) // Seulement les finalisés
            ->count();

        $this->info("Nombre de paiements à archiver: {$count}");

        if ($count === 0) {
            $this->info('Aucun paiement à archiver.');

            return;
        }

        if ($dryRun) {
            $this->table(
                ['ID', 'Référence', 'Statut', 'Date création'],
                Paiement::where('created_at', '<', $cutoffDate)
                    ->whereIn('statut', ['VERIFIED', 'REJECTED'])
                    ->limit(10)
                    ->get(['id', 'reference', 'statut', 'created_at'])
                    ->map(fn ($p) => [
                        $p->id,
                        $p->reference,
                        $p->statut->label(),
                        $p->created_at->format('Y-m-d'),
                    ])
            );

            return;
        }

        if (! $this->confirm("Archiver {$count} paiements ? Cette action est irréversible.")) {
            return;
        }

        $this->info('Début de l\'archivage...');

        DB::transaction(function () use ($cutoffDate) {
            // Créer un fichier d'archive JSON
            $archiveData = Paiement::with(['candidat.utilisateur', 'concours'])
                ->where('created_at', '<', $cutoffDate)
                ->whereIn('statut', ['VERIFIED', 'REJECTED'])
                ->get()
                ->map(function ($paiement) {
                    return [
                        'id' => $paiement->id,
                        'reference' => $paiement->reference,
                        'montant' => $paiement->montant,
                        'statut' => $paiement->statut->value,
                        'candidat_email' => $paiement->candidat?->utilisateur?->email,
                        'concours' => $paiement->concours?->libelle_concours,
                        'created_at' => $paiement->created_at,
                        'validated_at' => $paiement->validated_at,
                        'archived_at' => now(),
                    ];
                });

            // Sauvegarder l'archive
            $archiveFile = 'archives/paiements_archive_' . now()->format('Y_m') . '.json';
            Storage::put($archiveFile, $archiveData->toJson(JSON_PRETTY_PRINT));

            // Supprimer les paiements archivés
            $deletedCount = Paiement::where('created_at', '<', $cutoffDate)
                ->whereIn('statut', ['VERIFIED', 'REJECTED'])
                ->delete();

            $this->info("Archivage terminé: {$deletedCount} paiements supprimés");
            $this->info("Archive créée: {$archiveFile}");
        });

        $this->info('Archivage terminé avec succès !');
    }
}
