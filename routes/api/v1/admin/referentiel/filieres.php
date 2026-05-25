<?php

use App\Http\Controllers\FiliereController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Référentiel Filières
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Liste des filières actives (pour les sélections)
Route::get('actives', [FiliereController::class, 'active']);

// Récupérer par code
Route::get('code/{code}', [FiliereController::class, 'showByCode']);

// CRUD standard
Route::get('/', [FiliereController::class, 'index']);
Route::post('/', [FiliereController::class, 'store']);
Route::get('{filiere}', [FiliereController::class, 'show']);
Route::put('{filiere}', [FiliereController::class, 'update']);
Route::delete('{filiere}', [FiliereController::class, 'destroy']);

// Toggle statut
Route::patch('{filiere}/toggle-status', [FiliereController::class, 'toggleStatus']);
