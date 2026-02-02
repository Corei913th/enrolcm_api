<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Planning\PlanningEpreuveController;

/**
 * Routes Admin - Planning des Épreuves
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// CRUD Planning
Route::get('/', [PlanningEpreuveController::class, 'indexWithQuery']); // Support query params: ?concours_id=X&session_id=Y
Route::get('concours/{concours}/sessions/{session}', [PlanningEpreuveController::class, 'index']);
Route::post('/', [PlanningEpreuveController::class, 'store']);
Route::get('{planning}', [PlanningEpreuveController::class, 'show']);
Route::put('{planning}', [PlanningEpreuveController::class, 'update']);
Route::delete('{planning}', [PlanningEpreuveController::class, 'destroy']);
