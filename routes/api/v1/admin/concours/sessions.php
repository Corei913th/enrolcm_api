<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\ConcoursController;

/**
 * Routes Admin - Sessions du Concours
 * Prefix: /admin/concours/{concours}
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

Route::post('sessions', [ConcoursController::class, 'attachSession']);
Route::delete('sessions/{session}', [ConcoursController::class, 'detachSession']);
Route::put('sessions/{session}/state', [ConcoursController::class, 'changeSessionState']);
