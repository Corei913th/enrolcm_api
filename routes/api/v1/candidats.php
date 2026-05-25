<?php

use App\Http\Controllers\Candidats\CandidatController;
use Illuminate\Support\Facades\Route;

/**
 * ============================================
 * CANDIDATS ROUTES - API V1
 * ============================================
 *
 * Public routes for candidates
 * Admin routes for candidate management
 */

// --- Public Candidate Routes ---
Route::prefix('candidats')
    ->middleware(['auth:sanctum', 'role:CANDIDAT'])
    ->name('candidats.')
    ->group(function () {

        Route::get('/profile', [CandidatController::class, 'getProfile'])
            ->name('profile.show');

        Route::put('/profile', [CandidatController::class, 'updateProfile'])
            ->name('profile.update');

        Route::get('/candidatures', [CandidatController::class, 'getCandidatures'])
            ->name('candidatures.index');

        Route::post('/candidatures', [CandidatController::class, 'createCandidature'])
            ->name('candidatures.store');

        Route::get('/candidatures/{candidature}', [CandidatController::class, 'getCandidature'])
            ->name('candidatures.show');

        Route::get('/documents', [CandidatController::class, 'getDocuments'])
            ->name('documents.index');

        Route::post('/documents', [CandidatController::class, 'uploadDocument'])
            ->name('documents.upload');

    });

// --- Admin Candidate Management ---
Route::prefix('admin/candidats')
    ->middleware(['auth:sanctum', 'role:ADMIN'])
    ->name('admin.candidats.')
    ->group(function () {

        Route::get('/', [CandidatController::class, 'index'])
            ->name('index');

        Route::get('/{candidat}', [CandidatController::class, 'show'])
            ->name('show');

        Route::put('/{candidat}', [CandidatController::class, 'update'])
            ->name('update');

        Route::delete('/{candidat}', [CandidatController::class, 'destroy'])
            ->name('destroy');

        Route::post('/{candidat}/activate', [CandidatController::class, 'activate'])
            ->name('activate');

        Route::post('/{candidat}/deactivate', [CandidatController::class, 'deactivate'])
            ->name('deactivate');
    });
