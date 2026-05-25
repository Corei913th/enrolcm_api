<?php

namespace App\Jobs;

use App\Services\Infrastructure\Storage\TemporaryFileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupTempFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Durée de vie des fichiers temporaires en heures
     */
    private int $maxAgeHours;

    /**
     * Créer une nouvelle instance du job
     *
     * @param  int  $maxAgeHours  Nombre d'heures après lesquelles un fichier est considéré comme expiré
     */
    public function __construct(int $maxAgeHours = 24)
    {
        $this->maxAgeHours = $maxAgeHours;
    }

    /**
     * Exécuter le job de nettoyage des fichiers temporaires
     */
    public function handle(TemporaryFileService $temporaryFileService): void
    {
        try {
            Log::info('Démarrage du nettoyage des fichiers temporaires', [
                'max_age_hours' => $this->maxAgeHours,
            ]);

            $deletedCount = $temporaryFileService->cleanupExpired($this->maxAgeHours);

            Log::info('Nettoyage des fichiers temporaires terminé', [
                'deleted_count' => $deletedCount,
                'max_age_hours' => $this->maxAgeHours,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du nettoyage des fichiers temporaires', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-lancer l'exception pour que le job soit marqué comme échoué
            throw $e;
        }
    }
}
