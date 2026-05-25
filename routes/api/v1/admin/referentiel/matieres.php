<?php

use App\Http\Controllers\MatiereController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Référentiel Matières
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Liste des matières actives (pour les sélections)
Route::get('actives', [MatiereController::class, 'active']);

// Récupérer par code
Route::get('code/{code}', [MatiereController::class, 'showByCode']);

// CRUD standard
Route::get('/', [MatiereController::class, 'index']);
Route::post('/', [MatiereController::class, 'store']);
Route::get('{matiere}', [MatiereController::class, 'show']);
Route::put('{matiere}', [MatiereController::class, 'update']);
Route::delete('{matiere}', [MatiereController::class, 'destroy']);

// Toggle statut
Route::patch('{matiere}/toggle-status', [MatiereController::class, 'toggleStatus']);
