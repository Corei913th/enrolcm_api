<?php

use App\Http\Controllers\EcoleController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Référentiel Écoles
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Liste des écoles actives (pour les sélections)
Route::get('actives', [EcoleController::class, 'active']);

// Récupérer par code
Route::get('code/{code}', [EcoleController::class, 'showByCode']);

// CRUD standard
Route::get('/', [EcoleController::class, 'index']);
Route::post('/', [EcoleController::class, 'store']);
Route::get('{ecole}', [EcoleController::class, 'show']);
Route::put('{ecole}', [EcoleController::class, 'update']);
Route::post('{ecole}', [EcoleController::class, 'update']); // Support POST for file uploads
Route::delete('{ecole}', [EcoleController::class, 'destroy']);

// Toggle statut
Route::patch('{ecole}/toggle-status', [EcoleController::class, 'toggleStatus']);

// Gestion des fichiers
Route::post('{ecole}/upload-file', [EcoleController::class, 'uploadFile']);
Route::delete('{ecole}/delete-file', [EcoleController::class, 'deleteFile']);

// Génération de PDF
Route::post('{ecole}/generate-attestation', [EcoleController::class, 'generateAttestation']);
Route::get('{ecole}/preview-header', [EcoleController::class, 'previewHeader']);
