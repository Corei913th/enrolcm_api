<?php

use App\Http\Controllers\Centres\CentreController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Centres (Référentiel)
 * Prefix: /admin/referentiel/centres
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */
Route::get('/', [CentreController::class, 'index']);
Route::post('/', [CentreController::class, 'store']);
Route::get('active', [CentreController::class, 'active']);
Route::get('{id}', [CentreController::class, 'show']);
Route::put('{id}', [CentreController::class, 'update']);
Route::patch('{id}', [CentreController::class, 'update']);
Route::delete('{id}', [CentreController::class, 'destroy']);
