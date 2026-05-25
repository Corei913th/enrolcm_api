<?php

use App\Http\Controllers\Concours\SalleAffectationController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Affectations Salles
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Affectations par planning
Route::get('planning/{planning}', [SalleAffectationController::class, 'index']);
Route::post('planning/{planning}/affecter-salles', [SalleAffectationController::class, 'affecterSalles']);
Route::get('planning/{planning}/plan-salle', [SalleAffectationController::class, 'getPlanSalle']);
Route::get('planning/{planning}/stats', [SalleAffectationController::class, 'getStatistiquesAffectation']);

// Gestion individuelle des affectations
Route::put('{affectation}/reaffecter', [SalleAffectationController::class, 'reaffecterCandidat']);
Route::put('{affectation}/present', [SalleAffectationController::class, 'marquerPresent']);
