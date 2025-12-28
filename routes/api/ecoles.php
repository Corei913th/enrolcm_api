<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcoleController;

// Middleware désactivé temporairement pour les tests
Route::prefix('ecoles')->group(function () {
    // Liste des écoles actives (pour les sélections)
    Route::get('actives', [EcoleController::class, 'active']);
    
    // Récupérer par code
    Route::get('code/{code}', [EcoleController::class, 'showByCode']);
    
    // CRUD standard
    Route::get('/', [EcoleController::class, 'index']);
    Route::post('/', [EcoleController::class, 'store']);
    Route::get('{ecole}', [EcoleController::class, 'show']);
    Route::put('{ecole}', [EcoleController::class, 'update']);
    Route::delete('{ecole}', [EcoleController::class, 'destroy']);
    
    // Toggle statut
    Route::patch('{ecole}/toggle-status', [EcoleController::class, 'toggleStatus']);
    
    // Routes pour la gestion des fichiers
    Route::post('{ecole}/upload-file', [EcoleController::class, 'uploadFile']);
    Route::delete('{ecole}/delete-file', [EcoleController::class, 'deleteFile']);
    
    // Routes pour la génération de PDF
    Route::post('{ecole}/generate-attestation', [EcoleController::class, 'generateAttestation']);
    Route::get('{ecole}/preview-header', [EcoleController::class, 'previewHeader']);
});
