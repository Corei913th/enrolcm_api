<?php

use App\Http\Controllers\Candidat\CandidatureController;
use App\Http\Controllers\Candidat\ConvocationController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Candidat - Candidatures
 * Middleware appliqué : auth:sanctum + role:CANDIDAT
 */

// Liste des candidatures du candidat
Route::get('/', [CandidatureController::class, 'index'])
    ->name('candidatures.index');

// Détails d'une candidature
Route::get('/{id}', [CandidatureController::class, 'show'])
    ->name('candidatures.show');

// Résultat d'une candidature
Route::get('/{id}/resultat', [CandidatureController::class, 'resultat'])
    ->name('candidatures.resultat');

// Centres disponibles pour une candidature
Route::get('/{id}/centres', [CandidatureController::class, 'centres'])
    ->name('candidatures.centres');

// Compléter une candidature (centres)
Route::put('/{id}/complete', [CandidatureController::class, 'complete'])
    ->name('candidatures.complete');

// Télécharger la convocation pour une candidature
Route::get('/{id}/convocation', [ConvocationController::class, 'download'])
    ->name('convocation.download');

// Télécharger la fiche d'inscription pour une candidature
Route::get('/{id}/inscription-form', [CandidatureController::class, 'downloadInscriptionForm'])
    ->name('candidatures.inscription-form');

// Obtenir les capacités d'une candidature
Route::get('/{id}/capabilities', [CandidatureController::class, 'getCapabilities'])
    ->name('candidatures.capabilities');
// Paiement pour une candidature
Route::get('/{id}/paiement', [CandidatureController::class, 'getPaiement'])
    ->name('candidatures.paiement');

// Upload reçu de paiement
Route::post('/{id}/upload-payment-receipt', [CandidatureController::class, 'uploadPaymentReceipt'])
    ->name('candidatures.upload-payment-receipt');
