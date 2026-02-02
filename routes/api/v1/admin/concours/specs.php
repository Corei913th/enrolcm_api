<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\SpecConcoursController;

/**
 * Routes Admin - Spécialités Concours
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Form data pour les sélections
Route::get('form-data', [SpecConcoursController::class, 'formData']);

// CRUD Spécialités
Route::get('/', [SpecConcoursController::class, 'index']);
Route::post('/', [SpecConcoursController::class, 'store']);
Route::get('{spec}', [SpecConcoursController::class, 'show']);
Route::put('{spec}', [SpecConcoursController::class, 'update']);
Route::delete('{spec}', [SpecConcoursController::class, 'destroy']);

// Toggle statut
Route::post('{id}/toggle-status', [SpecConcoursController::class, 'toggleStatus']);
