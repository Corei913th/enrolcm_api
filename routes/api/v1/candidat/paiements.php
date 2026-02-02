<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaiementController;

/**
 * Routes Candidat - Paiements
 * Middleware appliqué : auth:sanctum + role:CANDIDAT
 */

// Liste des paiements du candidat
Route::get('/', [PaiementController::class, 'index']);

// Paiements en attente
Route::get('pending', [PaiementController::class, 'pending']);

// Détails d'un paiement
Route::get('{id}', [PaiementController::class, 'show']);
