<?php

use App\Http\Controllers\Concours\ConcoursController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Sessions du Concours
 * Prefix: /admin/concours/{concours}
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */
Route::post('sessions', [ConcoursController::class, 'attachSession']);
Route::delete('sessions/{session}', [ConcoursController::class, 'detachSession']);
Route::put('sessions/{session}/state', [ConcoursController::class, 'changeSessionState']);
