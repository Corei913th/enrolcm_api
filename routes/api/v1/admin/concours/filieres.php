<?php

use App\Http\Controllers\Concours\ConcoursController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Filières du Concours
 * Prefix: /admin/concours/{concours}
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */
Route::get('filieres/disponibles', [ConcoursController::class, 'listFilieresDisponibles']);
Route::get('filieres', [ConcoursController::class, 'listFilieresAttachees']);
Route::post('filieres', [ConcoursController::class, 'attachFiliere']);
Route::delete('filieres/{filiereId}', [ConcoursController::class, 'detachFiliere']);
Route::put('filieres/{filiereId}/places', [ConcoursController::class, 'updateFiliereNombrePlaces']);
