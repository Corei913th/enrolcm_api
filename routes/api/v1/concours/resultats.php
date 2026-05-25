<?php

use App\Http\Controllers\Concours\ResultatController;
use Illuminate\Support\Facades\Route;

/**
 * ============================================
 * RESULTATS ROUTES
 * ============================================
 *
 * Context: /admin/concours/{concours}/sessions/{session}/resultats
 *
 * Manages results calculation and publication
 */
Route::get('/', [ResultatController::class, 'index'])
    ->name('index');

Route::post('/calculer', [ResultatController::class, 'calculer'])
    ->name('calculer');

Route::post('/determiner-admissions', [ResultatController::class, 'determinerAdmissions'])
    ->name('admissions');

Route::post('/publier', [ResultatController::class, 'publier'])
    ->name('publier');

Route::get('/statistiques', [ResultatController::class, 'statistiques'])
    ->name('stats');
