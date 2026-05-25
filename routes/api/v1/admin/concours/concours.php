<?php

use App\Http\Controllers\Concours\ConcoursController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Concours
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Routes globales (sans ID de concours)
Route::get('availables', [ConcoursController::class, 'availables']);
Route::get('/', [ConcoursController::class, 'index']);
Route::post('/', [ConcoursController::class, 'store']);

// Fiche d'inscription PDF
Route::get('candidatures/{candidature}/fiche-inscription', [ConcoursController::class, 'telechargerFicheInscription']);

// Routes pour les specs (AVANT {concours} pour éviter les conflits)
Route::prefix('specs')->group(__DIR__ . '/specs.php');

// Routes spécifiques à un concours
Route::prefix('{concours}')->group(function () {
    // CRUD Principal
    Route::get('/', [ConcoursController::class, 'show']);
    Route::put('/', [ConcoursController::class, 'update']);
    Route::patch('/', [ConcoursController::class, 'update']);
    Route::delete('/', [ConcoursController::class, 'destroy']);

    // Actions
    Route::post('activate', [ConcoursController::class, 'activate']);
    Route::post('deactivate', [ConcoursController::class, 'deactivate']);
    Route::patch('assign-spec', [ConcoursController::class, 'assignSpec']);
    Route::post('configure-payment', [ConcoursController::class, 'configurePayment']);

    // Stats
    Route::get('stats', [ConcoursController::class, 'stats']);
    Route::get('validate-places', [ConcoursController::class, 'validatePlaces']);
    Route::get('places-report', [ConcoursController::class, 'placesReport']);
    Route::get('candidats', [ConcoursController::class, 'listCandidats']);

    // Sous-domaines
    require __DIR__ . '/filieres.php';
    require __DIR__ . '/epreuves.php';
    require __DIR__ . '/sessions.php';

    Route::prefix('centres')->group(__DIR__ . '/centres.php');

    Route::prefix('sessions/{session}')->group(__DIR__ . '/notes.php');
});
