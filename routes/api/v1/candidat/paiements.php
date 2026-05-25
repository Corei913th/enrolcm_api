<?php

use App\Http\Controllers\Payment\PaiementController;
use Illuminate\Support\Facades\Route;

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
