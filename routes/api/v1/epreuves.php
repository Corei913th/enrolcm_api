<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EpreuveController;

/**
 * ============================================
 * ÉPREUVES ROUTES - API V1
 * ============================================
 * 
 * Manages exam definitions (independent of concours)
 */

Route::prefix('admin/epreuves')
  ->middleware(['auth:sanctum', 'role:ADMIN'])
  ->name('admin.epreuves.')
  ->group(function () {

    Route::get('/', [EpreuveController::class, 'index'])
      ->name('index');

    Route::post('/', [EpreuveController::class, 'store'])
      ->name('store');

    Route::get('/{epreuve}', [EpreuveController::class, 'show'])
      ->name('show');

    Route::put('/{epreuve}', [EpreuveController::class, 'update'])
      ->name('update');

    Route::delete('/{epreuve}', [EpreuveController::class, 'destroy'])
      ->name('destroy');

    Route::post('/{epreuve}/activate', [EpreuveController::class, 'activate'])
      ->name('activate');

    Route::post('/{epreuve}/deactivate', [EpreuveController::class, 'deactivate'])
      ->name('deactivate');
  });
