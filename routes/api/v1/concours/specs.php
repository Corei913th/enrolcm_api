<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\SpecConcoursController;

/**
 * ============================================
 * SPECIALTIES (SPECS) ROUTES
 * ============================================
 * 
 * Context: /admin/concours/{concours}/specs
 * 
 * Manages specialty configurations for concours
 */

Route::prefix('{concours}/specs')->name('specs.')->group(function () {
  Route::get('/', [SpecConcoursController::class, 'index'])
    ->name('index');

  Route::post('/', [SpecConcoursController::class, 'store'])
    ->name('store');

  Route::get('/{spec}', [SpecConcoursController::class, 'show'])
    ->name('show');

  Route::put('/{spec}', [SpecConcoursController::class, 'update'])
    ->name('update');

  Route::delete('/{spec}', [SpecConcoursController::class, 'destroy'])
    ->name('destroy');
});
