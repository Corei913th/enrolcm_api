<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\ConcoursEpreuveController;

/**
 * Routes Admin - Épreuves du Concours
 * Prefix: /admin/concours/{concours}
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

Route::get('epreuves/disponibles', [ConcoursEpreuveController::class, 'disponibles']);
Route::get('epreuves', [ConcoursEpreuveController::class, 'index']);
Route::get('sessions/{session}/epreuves', [ConcoursEpreuveController::class, 'indexBySession']);
Route::post('epreuves', [ConcoursEpreuveController::class, 'attach']);
Route::put('epreuves/{epreuveId}', [ConcoursEpreuveController::class, 'updateParams']);
Route::delete('epreuves/{epreuveId}', [ConcoursEpreuveController::class, 'detach']);
