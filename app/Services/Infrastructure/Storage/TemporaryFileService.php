<?php

namespace App\Services\Infrastructure\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemporaryFileService
{
    /**
     * Chemin de base pour les fichiers temporaires
     */
    private const TEMP_BASE_PATH = 'temp/uploads';

    /**
     * Stocker un fichier temporairement
     *
     * @param  UploadedFile  $file  Le fichier à stocker
     * @param  string  $prefix  Préfixe pour le nom du fichier
     * @return string Le chemin du fichier stocké
     */
    public function storeTemporary(UploadedFile $file, string $prefix = ''): string
    {
        // Générer un nom de fichier unique
        $uniqueId = Str::uuid()->toString();
        $extension = $file->getClientOriginalExtension();
        $filename = $prefix ? "{$prefix}_{$uniqueId}.{$extension}" : "{$uniqueId}.{$extension}";

        // Construire le chemin complet
        $path = self::TEMP_BASE_PATH . '/' . $filename;

        // Stocker le fichier
        Storage::put($path, file_get_contents($file->getRealPath()));

        Log::info('Fichier temporaire stocké', [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ]);

        return $path;
    }

    /**
     * Déplacer un fichier temporaire vers un emplacement permanent
     *
     * @param  string  $tempPath  Chemin du fichier temporaire
     * @param  string  $permanentPath  Chemin de destination permanent
     * @return string Le chemin permanent du fichier
     *
     * @throws \Exception Si le fichier temporaire n'existe pas
     */
    public function moveToPermament(string $tempPath, string $permanentPath): string
    {
        if (! Storage::exists($tempPath)) {
            throw new \Exception("Le fichier temporaire n'existe pas: {$tempPath}");
        }

        // Créer le répertoire de destination si nécessaire
        $directory = dirname($permanentPath);
        if (! Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        // Déplacer le fichier
        Storage::move($tempPath, $permanentPath);

        Log::info("Fichier déplacé vers l'emplacement permanent", [
            'from' => $tempPath,
            'to' => $permanentPath,
        ]);

        return $permanentPath;
    }

    /**
     * Nettoyer les fichiers temporaires expirés
     *
     * @param  int  $hours  Nombre d'heures après lesquelles un fichier est considéré comme expiré
     * @return int Nombre de fichiers supprimés
     */
    public function cleanupExpired(int $hours = 24): int
    {
        try {
            if (! Storage::exists(self::TEMP_BASE_PATH)) {
                return 0;
            }

            $files = Storage::files(self::TEMP_BASE_PATH);
            $deletedCount = 0;
            $cutoffTime = now()->subHours($hours)->timestamp;

            foreach ($files as $file) {
                $lastModified = Storage::lastModified($file);

                if ($lastModified < $cutoffTime) {
                    Storage::delete($file);
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                Log::info('Nettoyage des fichiers temporaires', [
                    'deleted_count' => $deletedCount,
                    'max_age_hours' => $hours,
                ]);
            }

            return $deletedCount;
        } catch (\Exception $e) {
            logServiceError('Erreur lors du nettoyage des fichiers temporaires', $e);
            throw $e;
        }
    }

    /**
     * Obtenir le chemin complet d'un fichier temporaire
     *
     * @param  string  $filename  Nom du fichier
     * @return string Chemin complet
     */
    public function getTempPath(string $filename): string
    {
        return self::TEMP_BASE_PATH . '/' . $filename;
    }

    /**
     * Vérifier si un fichier temporaire existe
     *
     * @param  string  $path  Chemin du fichier
     */
    public function exists(string $path): bool
    {
        return Storage::exists($path);
    }

    /**
     * Supprimer un fichier temporaire spécifique
     *
     * @param  string  $path  Chemin du fichier
     */
    public function delete(string $path): bool
    {
        if (Storage::exists($path)) {
            return Storage::delete($path);
        }

        return false;
    }
}
