<?php

namespace App\Services\Infrastructure\Storage;

use App\Models\Ecole;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Service de gestion des fichiers des écoles
 * 
 * Service technique spécialisé pour l'upload, la validation et la suppression 
 * des fichiers des écoles (logo, emblème, header_frame).
 * 
 * Responsabilités :
 * - Upload de fichiers avec validation
 * - Suppression de fichiers
 * - Génération de noms de fichiers uniques
 * - Gestion du stockage des fichiers
 */
class EcoleFileStorageService
{
  /**
   * Types de fichiers autorisés pour les écoles
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
    'logo' => 2048,        // 2MB
    'embleme' => 2048,     // 2MB
    'header_frame' => 5120, // 5MB
  ];

  /**
   * Upload un fichier pour une école
   * 
   * @param Ecole $ecole École concernée
   * @param UploadedFile $file Fichier uploadé
   * @param string $type Type de fichier: 'logo', 'embleme', 'header_frame'
   * @return array Informations du fichier uploadé
   * @throws \InvalidArgumentException Si le fichier n'est pas valide
   */
  public function uploadFile(Ecole $ecole, UploadedFile $file, string $type): array
  {
    Log::info("EcoleFileStorageService::uploadFile - START", [
      'ecole_id' => $ecole->id,
      'type' => $type,
      'file_name' => $file->getClientOriginalName(),
      'file_size' => $file->getSize(),
      'file_mime' => $file->getMimeType(),
    ]);

    $this->validateFile($file, $type);

    Log::info("EcoleFileStorageService::uploadFile - File validated");

    $this->deleteOldFile($ecole, $type);

    Log::info("EcoleFileStorageService::uploadFile - Old file deleted");

    $filename = $this->generateFilename($file, $type);
    $directory = "ecoles/{$ecole->id}";
    $path = "{$directory}/{$filename}";

    Log::info("EcoleFileStorageService::uploadFile - Storing file", [
      'directory' => $directory,
      'filename' => $filename,
      'path' => $path,
    ]);

    Storage::disk('public')->putFileAs($directory, $file, $filename);

    Log::info("EcoleFileStorageService::uploadFile - File stored successfully", [
      'path' => $path,
      'exists' => Storage::disk('public')->exists($path),
    ]);

    $result = [
      'path' => $path,
      'original_name' => $file->getClientOriginalName(),
      'url' => asset('storage/' . $path),
    ];

    Log::info("EcoleFileStorageService::uploadFile - COMPLETE", $result);

    return $result;
  }

  /**
   * Supprimer un fichier d'une école
   * 
   * @param Ecole $ecole École concernée
   * @param string $type Type de fichier: 'logo', 'embleme', 'header_frame'
   * @return bool True si la suppression a réussi
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
      logServiceError("Erreur lors de la suppression du fichier {$type}", $e, ['ecole_id' => $ecole->id]);

      return false;
    }
  }

  /**
   * Supprimer tous les fichiers d'une école
   * 
   * @param Ecole $ecole École concernée
   * @return void
   */
  public function deleteAllFiles(Ecole $ecole): void
  {
    $this->deleteFile($ecole, 'logo');
    $this->deleteFile($ecole, 'embleme');
    $this->deleteFile($ecole, 'header_frame');

    // Supprimer le dossier s'il est vide
    $directory = "ecoles/{$ecole->id}";
    if (Storage::disk('public')->exists($directory)) {
      $files = Storage::disk('public')->files($directory);
      if (empty($files)) {
        Storage::disk('public')->deleteDirectory($directory);
      }
    }
  }

  /**
   * Valider un fichier uploadé
   * 
   * @param UploadedFile $file Fichier à valider
   * @param string $type Type de fichier
   * @return void
   * @throws \InvalidArgumentException Si le fichier n'est pas valide
   */
  private function validateFile(UploadedFile $file, string $type): void
  {
    // Vérifier le type MIME
    if (!in_array($file->getMimeType(), self::ALLOWED_TYPES[$type])) {
      throw new \InvalidArgumentException(
        "Type de fichier non autorisé pour {$type}. Types acceptés : " .
          implode(', ', self::ALLOWED_TYPES[$type])
      );
    }

    // Vérifier la taille
    $maxSize = self::MAX_SIZES[$type] * 1024; // Convertir en bytes
    if ($file->getSize() > $maxSize) {
      throw new \InvalidArgumentException(
        "Fichier trop volumineux pour {$type}. Taille maximale : " .
          self::MAX_SIZES[$type] . " Ko"
      );
    }

    // Vérifier la validité du fichier
    if (!$file->isValid()) {
      throw new \InvalidArgumentException("Fichier invalide ou corrompu");
    }
  }

  /**
   * Supprimer l'ancien fichier avant d'uploader le nouveau
   * 
   * @param Ecole $ecole École concernée
   * @param string $type Type de fichier
   * @return void
   */
  private function deleteOldFile(Ecole $ecole, string $type): void
  {
    $this->deleteFile($ecole, $type);
  }

  /**
   * Générer un nom de fichier unique
   * 
   * @param UploadedFile $file Fichier uploadé
   * @param string $type Type de fichier
   * @return string Nom de fichier unique
   */
  private function generateFilename(UploadedFile $file, string $type): string
  {
    $extension = $file->getClientOriginalExtension();
    $timestamp = now()->format('YmdHis');
    $random = Str::random(8);

    return "{$type}_{$timestamp}_{$random}.{$extension}";
  }

  /**
   * Obtenir les informations d'un fichier
   * 
   * @param Ecole $ecole École concernée
   * @param string $type Type de fichier
   * @return array|null Informations du fichier ou null si inexistant
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
