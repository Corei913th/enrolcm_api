<?php

namespace App\Console\Commands;

use App\Jobs\AutoValiderPaiementsJob;
use Illuminate\Console\Command;

class AutoValiderPaiements extends Command
{
    protected $signature = 'paiements:auto-valider';

    protected $description = 'Auto-valider les paiements OCR vérifiés';

    public function handle(): int
    {
        $this->info('Lancement de l\'auto-validation des paiements...');

        AutoValiderPaiementsJob::dispatch();

        $this->info('Job d\'auto-validation lancé avec succès');

        return Command::SUCCESS;
    }
}
