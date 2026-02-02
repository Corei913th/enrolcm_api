<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartementController;

/**
 * Routes Admin - Référentiel Départements
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// CRUD standard
Route::get('/', [DepartementController::class, 'index']);
Route::post('/', [DepartementController::class, 'store']);
Route::get('{departement}', [DepartementController::class, 'show']);
Route::put('{departement}', [DepartementController::class, 'update']);
Route::delete('{departement}', [DepartementController::class, 'destroy']);

// Activation/Désactivation
Route::post('{departement}/activate', [DepartementController::class, 'activate']);
Route::post('{departement}/deactivate', [DepartementController::class, 'deactivate']);

// Statistiques
Route::get('{departement}/stats', [DepartementController::class, 'stats']);
