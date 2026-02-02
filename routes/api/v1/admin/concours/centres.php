<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\ConcoursController;

/**
 * Routes Admin - Centres du Concours
 * Prefix: /admin/concours/{concours}/centres
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

Route::get('/', [ConcoursController::class, 'listCentres']);
Route::post('/', [ConcoursController::class, 'attachCentre']);
Route::patch('{centreId}', [ConcoursController::class, 'updateCentreStatus']);
Route::post('sync', [ConcoursController::class, 'syncCentres']);
Route::delete('{centreId}', [ConcoursController::class, 'detachCentre']);
