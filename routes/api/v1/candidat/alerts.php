<?php

use App\Http\Controllers\Candidat\AlertController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Candidat - Alertes/Notifications
 * Middleware appliqué : auth:sanctum + role:CANDIDAT
 */

// Liste des alertes
Route::get('/', [AlertController::class, 'index']);

// Marquer une alerte comme lue
Route::post('{alertId}/dismiss', [AlertController::class, 'dismiss']);

// Marquer toutes les alertes comme lues
Route::post('dismiss-all', [AlertController::class, 'dismissAll']);
