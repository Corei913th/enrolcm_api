<?php

use Illuminate\Support\Facades\Route;

/**
 * Routes Publiques API v1
 *
 * Routes accessibles sans authentification
 */

// Authentification
Route::prefix('auth')->group(__DIR__ . '/auth.php');

// Inscription optimisée
Route::prefix('registration')->group(__DIR__ . '/registration.php');

// Données publiques
Route::prefix('regions')->group(__DIR__ . '/regions.php');

// Sessions disponibles (publiques)
Route::prefix('sessions')->group(__DIR__ . '/sessions.php');

// Concours publics
Route::prefix('concours')->group(__DIR__ . '/concours.php');
