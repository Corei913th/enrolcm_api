<?php

use App\Http\Controllers\Admin\Stats\StatsController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Stats
 * Prefix: /admin/stats
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

Route::get('global', [StatsController::class, 'global']);
Route::get('dashboard', [StatsController::class, 'dashboard']);
Route::get('widgets', [StatsController::class, 'widgets']);
Route::get('ecoles', [StatsController::class, 'ecolesStats']);
Route::get('ecoles-detaillees', [StatsController::class, 'ecoles']);
Route::get('departements', [StatsController::class, 'departementsStats']);
Route::get('filieres', [StatsController::class, 'filieresStats']);
Route::get('niveaux', [StatsController::class, 'niveauxStats']);
Route::get('centres', [StatsController::class, 'centres']);
Route::get('centres-stats', [StatsController::class, 'centresStats']);
Route::get('regions', [StatsController::class, 'regions']);
Route::get('concours-stats', [StatsController::class, 'concoursStats']);
Route::get('concours/{concoursId}', [StatsController::class, 'concours']);
Route::get('paiements', [StatsController::class, 'paiements']);
Route::get('documents', [StatsController::class, 'documents']);
Route::get('timeline', [StatsController::class, 'timeline']);
Route::get('comparatives', [StatsController::class, 'comparatives']);
