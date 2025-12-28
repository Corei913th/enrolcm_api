<?php

namespace App\Jobs;

use App\Services\Payment\PaymentReferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NettoyerPRUExpireesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nettoyer les PRU expirées
     */
    public function handle(PaymentReferenceService $pruService): void
    {
        try {
            $count = $pruService->nettoyerPRUExpirees();

            if ($count > 0) {
                Log::info("Nettoyage PRU expirées: {$count} référence(s) supprimée(s)");
            }
        } catch (\Exception $e) {
            Log::error("Erreur nettoyage PRU expirées: {$e->getMessage()}");
        }
    }
}
