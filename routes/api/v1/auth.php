<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

/**
 * ============================================
 * AUTHENTICATION ROUTES - API V1
 * ============================================
 */

/*Route::prefix('auth')->name('auth.')->group(function () {

  // Registration
  Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

  // Login
  Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login');

  // Logout (requires auth)
  Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');

  // Current user info
  Route::get('/user', [AuthenticatedSessionController::class, 'show'])
    ->middleware('auth:sanctum')
    ->name('user');

  // Password reset
  Route::post('/forgot-password', [AuthenticatedSessionController::class, 'forgotPassword'])
    ->name('password.forgot');

  Route::post('/reset-password', [AuthenticatedSessionController::class, 'resetPassword'])
    ->name('password.reset');
});*/
