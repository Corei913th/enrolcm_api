<?php

use App\Http\Controllers\Candidats\CandidatController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Gestion des Candidats
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Liste et statistiques
Route::get('/', [CandidatController::class, 'index']);
Route::get('stats', [CandidatController::class, 'stats']);

// Recherche par PRU
Route::get('pru/{pru}', [CandidatController::class, 'getByPRU']);

// Détails candidat
Route::get('{id}', [CandidatController::class, 'show']);

// Activation/Désactivation
Route::post('{id}/activate', [CandidatController::class, 'activate']);
Route::post('{id}/deactivate', [CandidatController::class, 'deactivate']);
