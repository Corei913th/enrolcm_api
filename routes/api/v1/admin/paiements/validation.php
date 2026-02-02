<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaiementValidationController;

/**
 * Routes Admin - Validation des Paiements
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Liste tous les paiements avec priorité pour les paiements manuels
Route::get('/', [PaiementValidationController::class, 'index']);

// Liste des paiements en attente
Route::get('en-attente', [PaiementValidationController::class, 'enAttente']);

// Get validation statistics
Route::get('stats', [PaiementValidationController::class, 'stats']);

// Validation/Rejet de paiements
Route::post('{paiement}/valider', [PaiementValidationController::class, 'valider']);
Route::post('{paiement}/rejeter', [PaiementValidationController::class, 'rejeter']);
