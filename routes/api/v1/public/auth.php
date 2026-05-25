<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Payment\PaiementController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Publiques - Authentification
 * Pas de middleware d'authentification
 */

// Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::post('check-email', [AuthController::class, 'checkEmail']);

// Route::post('candidat/register', [CandidatController::class, 'register']);
Route::post('candidat/login', [AuthController::class, 'loginCandidat']);
// Route::post('candidat/verify-pru', [CandidatController::class, 'verifyPRU']);

Route::post('paiements', [PaiementController::class, 'store']);
// Route::post('paiements/verify-pru', [PaiementController::class, 'verifyPRU']);

// Email Verification Routes
Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('email/resend', [AuthController::class, 'resendVerificationEmail'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
});
