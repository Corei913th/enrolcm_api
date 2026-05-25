<?php

use App\Http\Controllers\Concours\ResultatController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Candidat - Résultats et Timer
 *
 * Routes pour la consultation des résultats et le timer d'attente
 */

// Obtenir la date de publication pour le timer (pour chaque candidature du candidat)
Route::get('/timer/{concoursId}/{sessionId}', [ResultatController::class, 'getDatePublication'])
    ->name('candidat.resultats.timer');

// Voir les résultats individuels
Route::get('/{concoursId}/{sessionId}/{candidatureId}', [ResultatController::class, 'getResultatCandidat'])
    ->name('candidat.resultats.voir');
