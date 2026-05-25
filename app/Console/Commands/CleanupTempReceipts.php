<?php

namespace App\Console\Commands;

use App\Jobs\CleanupTempReceiptsJob;
use Illuminate\Console\Command;

class CleanupTempReceipts extends Command
{
    protected $signature = 'receipts:cleanup-temp {--hours=24 : Âge maximum des fichiers en heures}';

    protected $description = 'Nettoyer les fichiers temporaires de reçus abandonnés';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');

        $this->info("Nettoyage des fichiers temporaires de plus de {$hours} heures...");

        CleanupTempReceiptsJob::dispatch($hours);

        $this->info('Job de nettoyage lancé avec succès');

        return Command::SUCCESS;
    }
}
