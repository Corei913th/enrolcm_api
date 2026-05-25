<?php

namespace App\Jobs;

use App\Models\Paiement;
use App\Services\Domain\Paiement\PaiementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoValiderPaiementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Traiter l'auto-validation des paiements OCR vérifiés
     */
    public function handle(PaiementService $paiementService): void
    {
        try {
            $paiements = Paiement::ocrVerifie()
                ->orderBy('created_at', 'asc')
                ->limit(100)
                ->get();

            $valides = 0;
            $exceptions = 0;

            foreach ($paiements as $paiement) {
                try {
                    if ($paiementService->autoValidate($paiement)) {
                        $valides++;
                    } else {
                        $exceptions++;
                    }
                } catch (\Exception $e) {
                    Log::error("Erreur auto-validation paiement {$paiement->id}: {$e->getMessage()}");
                    $exceptions++;
                }
            }

            if ($valides > 0 || $exceptions > 0) {
                Log::info("Auto-validation paiements: {$valides} validés, {$exceptions} exceptions");
            }
        } catch (\Exception $e) {
            Log::error("Erreur job auto-validation paiements: {$e->getMessage()}");
        }
    }
}
