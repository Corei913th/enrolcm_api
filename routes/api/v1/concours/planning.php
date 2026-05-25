<?php

use App\Http\Controllers\Planning\PlanningEpreuveController;
use Illuminate\Support\Facades\Route;

/**
 * ============================================
 * PLANNING ÉPREUVES ROUTES
 * ============================================
 *
 * Context: /admin/concours/{concours}/sessions/{session}/plannings
 *
 * Manages exam scheduling for a specific concours session
 */
Route::get('/', [PlanningEpreuveController::class, 'index'])
    ->name('index');

Route::post('/', [PlanningEpreuveController::class, 'store'])
    ->name('store');

Route::get('/{planning}', [PlanningEpreuveController::class, 'show'])
    ->name('show');

Route::put('/{planning}', [PlanningEpreuveController::class, 'update'])
    ->name('update');

Route::delete('/{planning}', [PlanningEpreuveController::class, 'destroy'])
    ->name('destroy');
