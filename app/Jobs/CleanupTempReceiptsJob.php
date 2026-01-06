<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanupTempReceiptsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Durée de vie des fichiers temporaires en heures
     */
    private int $maxAgeHours;

    public function __construct(int $maxAgeHours = 24)
    {
        $this->maxAgeHours = $maxAgeHours;
    }

    /**
     * Nettoyer les fichiers temporaires de reçus abandonnés
     */
    public function handle(): void
    {
        try {
            $tempPath = 'receipts/temp';
            
            if (!Storage::exists($tempPath)) {
                return;
            }

            $files = Storage::files($tempPath);
            $deletedCount = 0;
            $cutoffTime = now()->subHours($this->maxAgeHours)->timestamp;

            foreach ($files as $file) {
                $lastModified = Storage::lastModified($file);
                
                if ($lastModified < $cutoffTime) {
                    Storage::delete($file);
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                Log::info("Nettoyage des reçus temporaires: {$deletedCount} fichier(s) supprimé(s)");
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors du nettoyage des reçus temporaires: {$e->getMessage()}");
        }
    }
}
