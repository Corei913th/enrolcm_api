<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Candidat\ProfileController;

/**
 * Routes Candidat - Profil
 * Middleware appliqué : auth:sanctum + role:CANDIDAT
 */

// Récupérer le profil
Route::get('/', [ProfileController::class, 'show']);

// Mettre à jour le profil
Route::put('/', [ProfileController::class, 'update']);
