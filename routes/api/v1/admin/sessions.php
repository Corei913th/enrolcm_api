<?php

use App\Http\Controllers\Admin\SessionController;
use Illuminate\Support\Facades\Route;

/**
 * ============================================
 * SESSIONS ROUTES
 * ============================================
 *
 * Gestion des sessions académiques
 */

// Sessions disponibles (publiques - pas besoin d'auth pour lister)
Route::get('/disponibles', [SessionController::class, 'disponibles'])
    ->name('disponibles');

Route::get('/actives', [SessionController::class, 'actives'])
    ->name('actives');

// Routes protégées (nécessitent authentification)
Route::middleware(['auth:sanctum'])->group(function () {

    // Liste et création
    Route::get('/', [SessionController::class, 'index'])
        ->name('index');

    Route::post('/', [SessionController::class, 'store'])
        ->name('store');

    // Actions sur une session spécifique
    Route::prefix('{session}')->group(function () {

        Route::get('/', [SessionController::class, 'show'])
            ->name('show');

        Route::put('/', [SessionController::class, 'update'])
            ->name('update');

        Route::delete('/', [SessionController::class, 'destroy'])
            ->name('destroy');

        // Actions spécifiques
        Route::post('/activate', [SessionController::class, 'activate'])
            ->name('activate');

        Route::post('/deactivate', [SessionController::class, 'deactivate'])
            ->name('deactivate');

        Route::get('/stats', [SessionController::class, 'stats'])
            ->name('stats');
    });
});
