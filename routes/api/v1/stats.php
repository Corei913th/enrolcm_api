<?php

use App\Http\Controllers\Admin\Stats\StatsController;
use Illuminate\Support\Facades\Route;

/**
 * ============================================
 * STATISTICS ROUTES - API V1
 * ============================================
 */
Route::prefix('admin/stats')
    ->middleware(['auth:sanctum', 'role:ADMIN'])
    ->name('admin.stats.')
    ->group(function () {

        // Global stats
        Route::get('/global', [StatsController::class, 'global'])
            ->name('global');

        // Concours stats
        Route::get('/concours', [StatsController::class, 'concours'])
            ->name('concours');

        Route::get('/concours/{concours}', [StatsController::class, 'concoursDetail'])
            ->name('concours.detail');

        // Candidatures stats
        Route::get('/candidatures', [StatsController::class, 'candidatures'])
            ->name('candidatures');

        // Payments stats
        Route::get('/paiements', [StatsController::class, 'paiements'])
            ->name('paiements');

        // Documents stats
        Route::get('/documents', [StatsController::class, 'documents'])
            ->name('documents');
    });
