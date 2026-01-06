<?php

namespace App\Console\Commands;

use App\Jobs\NettoyerPRUExpireesJob;
use Illuminate\Console\Command;

class NettoyerPRUExpirees extends Command
{
    protected $signature = 'pru:nettoyer';
    protected $description = 'Nettoyer les références de paiement (PRU) expirées';

    public function handle(): int
    {
        $this->info('Nettoyage des PRU expirées...');
        
        NettoyerPRUExpireesJob::dispatch();
        
        $this->info('Job de nettoyage lancé avec succès');
        
        return Command::SUCCESS;
    }
}
