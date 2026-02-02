<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SessionController;

/**
 * ============================================
 * SESSIONS PUBLIQUES
 * ============================================
 *
 */

// Sessions disponibles pour inscription
Route::get('/disponibles', [SessionController::class, 'disponibles'])
  ->name('public.sessions.disponibles');

// Sessions actives
Route::get('/actives', [SessionController::class, 'actives'])
  ->name('public.sessions.actives');

// Liste de toutes les sessions (avec filtres)
Route::get('/', [SessionController::class, 'index'])
  ->name('public.sessions.index');

// Détails d'une session
Route::get('/{session}', [SessionController::class, 'show'])
  ->name('public.sessions.show');
