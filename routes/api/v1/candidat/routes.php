<?php

use Illuminate\Support\Facades\Route;


// Profil candidat
Route::prefix('profile')->group(__DIR__ . '/profile.php');

// Candidatures
Route::prefix('candidatures')->group(__DIR__ . '/candidatures.php');

// Documents
Route::prefix('documents')->group(__DIR__ . '/documents.php');

// Paiements
Route::prefix('paiements')->group(__DIR__ . '/paiements.php');

// Dashboard
Route::prefix('dashboard')->group(__DIR__ . '/dashboard.php');

// Alertes/Notifications
Route::prefix('alerts')->group(__DIR__ . '/alerts.php');

// Résultats et Timer
Route::prefix('resultats')->group(__DIR__ . '/resultats.php');
