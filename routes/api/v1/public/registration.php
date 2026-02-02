<?php

use App\Http\Controllers\Public\RegistrationController;
use Illuminate\Support\Facades\Route;

/**
 * Routes d'inscription publiques
 */

// Récupérer les informations du concours (filières, sessions, etc.)
Route::get('/concours/{concours}', [RegistrationController::class, 'getConcoursInfo'])
  ->middleware('throttle:60,1') // 60 requêtes par minute
  ->name('registration.concours-info');

// Étape 1: Vérifier l'éligibilité
Route::post('/check-eligibility', [RegistrationController::class, 'checkEligibility'])
  ->middleware('throttle:25,60') // 10 requêtes par heure
  ->name('registration.check-eligibility');

// Étape 2: Upload de la preuve de paiement
Route::post('/upload-payment', [RegistrationController::class, 'uploadPayment'])
  ->middleware('throttle:15,60') // 5 uploads par heure
  ->name('registration.upload-payment');

// Étape 2b: Validation manuelle du paiement (si OCR échoue)
Route::post('/validate-payment', [RegistrationController::class, 'validatePayment'])
  ->middleware('throttle:25,60') // 15 validations par heure
  ->name('registration.validate-payment');

// Étape 3: Compléter l'inscription
Route::post('/complete', [RegistrationController::class, 'complete'])
  ->middleware('throttle:25,60') // 5 tentatives par heure
  ->name('registration.complete');
