<?php

use App\Http\Controllers\Admin\EpreuveController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Épreuves
 * Prefix: /admin/epreuves
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */
Route::get('/', [EpreuveController::class, 'index']);
Route::post('/', [EpreuveController::class, 'store']);
Route::get('/{epreuve}', [EpreuveController::class, 'show']);
Route::put('/{epreuve}', [EpreuveController::class, 'update']);
Route::delete('/{epreuve}', [EpreuveController::class, 'destroy']);
Route::post('/{epreuve}/activate', [EpreuveController::class, 'activate']);
Route::post('/{epreuve}/deactivate', [EpreuveController::class, 'deactivate']);
