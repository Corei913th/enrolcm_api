<?php

use App\Http\Controllers\Candidat\ProfileController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Candidat - Profil
 * Middleware appliqué : auth:sanctum + role:CANDIDAT
 */

// Récupérer le profil
Route::get('/', [ProfileController::class, 'show']);

// Mettre à jour le profil
Route::put('/', [ProfileController::class, 'update']);
