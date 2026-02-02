<?php

use Illuminate\Support\Facades\Route;

/**
 * Routes API v1
 * 
 * Structure organisée par rôle et domaine métier :
 * - candidat/  : Routes pour les candidats authentifiés
 * - admin/     : Routes pour les administrateurs
 * - shared/    : Routes partagées entre plusieurs rôles
 */

// ============================================
// ROUTES PUBLIQUES (Sans authentification)
// ============================================
Route::group([], base_path('routes/api/v1/public/routes.php'));

// ============================================
// ROUTES CANDIDAT (auth:sanctum + role:CANDIDAT)
// ============================================
Route::prefix('candidat')
  ->middleware(['auth:sanctum', 'role:CANDIDAT'])
  ->group(base_path('routes/api/v1/candidat/routes.php'));

// ============================================
// ROUTES ADMIN (auth:sanctum + role:ADMIN)
// ============================================
Route::prefix('admin')
  ->middleware(['auth:sanctum', 'role:ADMIN'])
  ->group(base_path('routes/api/v1/admin/routes.php'));

// ============================================
// ROUTES PARTAGÉES (auth:sanctum)
// ============================================
Route::prefix('shared')
  ->middleware('auth:sanctum')
  ->group(base_path('routes/api/v1/shared/routes.php'));
