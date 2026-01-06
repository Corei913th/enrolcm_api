<?php

namespace App\Services\Ecoles;

use App\Models\Ecole;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EcoleFileService
{
    /**
     * Types de fichiers autorisés
     */
    private const ALLOWED_TYPES = [
        'logo' => ['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml'],
        'embleme' => ['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml'],
        'header_frame' => ['image/jpeg', 'image/png', 'image/jpg'],
    ];

    /**
     * Tailles maximales en Ko
     */
    private const MAX_SIZES = [
        'logo' => 2048, // 2MB
        'embleme' => 2048, // 2MB
        'header_frame' => 5120, // 5MB
    ];


    /**
     * Upload a file for a school
     *
     * @param Ecole $ecole School model
     * @param UploadedFile $file Uploaded file
     * @param string $type File type: 'logo', 'embleme', 'header_frame'
     * @return array File information array with 'path', 'original_name', 'url'
     */
    public function uploadFile(Ecole $ecole, UploadedFile $file, string $type): array
    {
        $this->validateFile($file, $type);
        $this->deleteOldFile($ecole, $type);
        $filename = $this->generateFilename($file, $type);
        $directory = "ecoles/{$ecole->id}";
        $path = "{$directory}/{$filename}";

        
        Storage::disk('public')->putFileAs($directory, $file, $filename);

        Log::info("Fichier {$type} uploadé pour l'école", [
            'ecole_id' => $ecole->id,
            'type' => $type,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ]);

        return [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'url' => asset('storage/' . $path),
        ];
    }

    /**
     * Delete a file for a school
     *
     * @param Ecole $ecole School model
     * @param string $type File type: 'logo', 'embleme', 'header_frame'
     * @return bool True if deletion successful
     */
    public function deleteFile(Ecole $ecole, string $type): bool
    {
        $pathField = "{$type}_path";

        if (!$ecole->$pathField) {
            return false;
        }

        try {
            if (Storage::disk('public')->exists($ecole->$pathField)) {
                Storage::disk('public')->delete($ecole->$pathField);

                Log::info("Fichier {$type} supprimé pour l'école", [
                    'ecole_id' => $ecole->id,
                    'type' => $type,
                    'path' => $ecole->$pathField,
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression du fichier {$type}", [
                'ecole_id' => $ecole->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Delete all files for a school
     *
     * @param Ecole $ecole School model
     * @return void
     */
    public function deleteAllFiles(Ecole $ecole): void
    {
        $this->deleteFile($ecole, 'logo');
        $this->deleteFile($ecole, 'embleme');
        $this->deleteFile($ecole, 'header_frame');

        $directory = "ecoles/{$ecole->id}";
        if (Storage::disk('public')->exists($directory)) {
            $files = Storage::disk('public')->files($directory);
            if (empty($files)) {
                Storage::disk('public')->deleteDirectory($directory);
            }
        }
    }

    /**
     * Validate a file
     *
     * @param UploadedFile $file Uploaded file
     * @param string $type File type: 'logo', 'embleme', 'header_frame'
     * @return void
     * @throws \InvalidArgumentException If file is not valid
     */
    private function validateFile(UploadedFile $file, string $type): void
    {
        if (!in_array($file->getMimeType(), self::ALLOWED_TYPES[$type])) {
            throw new \InvalidArgumentException(
                "Type de fichier non autorisé pour {$type}. Types acceptés : " .
                    implode(', ', self::ALLOWED_TYPES[$type])
            );
        }

        $maxSize = self::MAX_SIZES[$type] * 1024; // Convertir en bytes
        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException(
                "Fichier trop volumineux pour {$type}. Taille maximale : " .
                    self::MAX_SIZES[$type] . " Ko"
            );
        }

        if (!$file->isValid()) {
            throw new \InvalidArgumentException("Fichier invalide ou corrompu");
        }
    }

    /**
     * Delete the old file
     *
     * @param Ecole $ecole School model
     * @param string $type File type: 'logo', 'embleme', 'header_frame'
     * @return void
     */
    private function deleteOldFile(Ecole $ecole, string $type): void
    {
        $this->deleteFile($ecole, $type);
    }

    /**
     * Generate a unique filename
     *
     * @param UploadedFile $file Uploaded file
     * @param string $type File type: 'logo', 'embleme', 'header_frame'
     * @return string Unique filename
     */
    private function generateFilename(UploadedFile $file, string $type): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);

        return "{$type}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get file information
     *
     * @param Ecole $ecole School model
     * @param string $type File type: 'logo', 'embleme', 'header_frame'
     * @return array File information array with 'path', 'url', 'original_name', 'exists', 'size', 'mime_type'
     */
    public function getFileInfo(Ecole $ecole, string $type): ?array
    {
        $pathField = "{$type}_path";
        $nameField = "{$type}_original_name";

        if (!$ecole->$pathField) {
            return null;
        }

        $path = $ecole->$pathField;
        $exists = Storage::disk('public')->exists($path);

        return [
            'path' => $path,
            'url' => asset('storage/' . $path),
            'original_name' => $ecole->$nameField,
            'exists' => $exists,
            'size' => $exists ? Storage::disk('public')->size($path) : null,
            'mime_type' => $exists ? Storage::disk('public')->mimeType($path) : null,
        ];
    }
}
