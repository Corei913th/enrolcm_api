<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Candidat\DashboardController;

/**
 * Routes Candidat - Dashboard
 * Middleware appliqué : auth:sanctum + role:CANDIDAT
 */

// Statistiques du tableau de bord
Route::get('/stats', [DashboardController::class, 'stats'])
  ->name('dashboard.stats');

// Vue d'ensemble des candidatures avec capacités
Route::get('/overview', [DashboardController::class, 'overview'])
  ->name('dashboard.overview');
