<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\SalleAffectationController;

/**
 * ============================================
 * AFFECTATIONS SALLES ROUTES
 * ============================================
 * 
 * Context: /admin/concours/{concours}/sessions/{session}/affectations
 * 
 * Manages room assignments for candidates
 */

Route::get('/', [SalleAffectationController::class, 'index'])
  ->name('index');

Route::post('/affecter-salles', [SalleAffectationController::class, 'affecterSalles'])
  ->name('affecter');

Route::get('/plan-salle', [SalleAffectationController::class, 'getPlanSalle'])
  ->name('plan');

Route::get('/statistiques', [SalleAffectationController::class, 'getStatistiquesAffectation'])
  ->name('stats');

Route::put('/{affectation}/reaffecter', [SalleAffectationController::class, 'reaffecterCandidat'])
  ->name('reaffecter');

Route::put('/{affectation}/presence', [SalleAffectationController::class, 'marquerPresent'])
  ->name('presence');
