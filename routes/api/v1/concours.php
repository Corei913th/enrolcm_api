<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Concours\ConcoursController;

/**
 * ============================================
 * CONCOURS ROUTES - API V1
 * ============================================
 * 
 * Structure:
 * - Public routes (no auth)
 * - Admin routes (auth + ADMIN role)
 *   - CRUD operations
 *   - Relationships (sessions, centres, filières)
 *   - Sub-resources (specs, planning, notes)
 */

// ============================================
// PUBLIC ROUTES
// ============================================
Route::prefix('concours')->group(function () {
  Route::get('/', [ConcoursController::class, 'listPublic'])
    ->name('concours.public.index');

  Route::get('/{concours}', [ConcoursController::class, 'showPublic'])
    ->name('concours.public.show');
});

// ============================================
// ADMIN ROUTES
// ============================================
Route::prefix('admin/concours')
  ->middleware(['auth:sanctum', 'role:ADMIN'])
  ->name('admin.concours.')
  ->group(function () {

    // --- CRUD Operations ---
    Route::get('/', [ConcoursController::class, 'index'])
      ->name('index');

    Route::post('/', [ConcoursController::class, 'store'])
      ->name('store');

    Route::get('/{concours}', [ConcoursController::class, 'show'])
      ->name('show');

    Route::put('/{concours}', [ConcoursController::class, 'update'])
      ->name('update');

    Route::delete('/{concours}', [ConcoursController::class, 'destroy'])
      ->name('destroy');

    // --- Status Management ---
    Route::post('/{concours}/activate', [ConcoursController::class, 'activate'])
      ->name('activate');

    Route::post('/{concours}/deactivate', [ConcoursController::class, 'deactivate'])
      ->name('deactivate');

    // --- Statistics ---
    Route::get('/{concours}/stats', [ConcoursController::class, 'stats'])
      ->name('stats');

    // --- Candidats ---
    Route::get('/{concours}/candidats', [ConcoursController::class, 'listCandidats'])
      ->name('candidats.index');

    // --- Payment Configuration ---
    Route::post('/{concours}/configure-payment', [ConcoursController::class, 'configurePayment'])
      ->name('payment.configure');

    // --- Relationships: Sessions ---
    Route::prefix('{concours}/sessions')->name('sessions.')->group(function () {
      Route::post('/', [ConcoursController::class, 'attachSession'])
        ->name('attach');

      Route::delete('/{session}', [ConcoursController::class, 'detachSession'])
        ->name('detach');

      Route::put('/{session}/state', [ConcoursController::class, 'changeSessionState'])
        ->name('state.update');

      // Sub-resources for session context
      require __DIR__ . '/concours/session-context.php';
    });

    // --- Relationships: Centres ---
    Route::prefix('{concours}/centres')->name('centres.')->group(function () {
      Route::get('/', [ConcoursController::class, 'listCentres'])
        ->name('index');

      Route::post('/', [ConcoursController::class, 'attachCentre'])
        ->name('attach');

      Route::post('/sync', [ConcoursController::class, 'syncCentres'])
        ->name('sync');

      Route::patch('/{centre}', [ConcoursController::class, 'updateCentreStatus'])
        ->name('update');

      Route::delete('/{centre}', [ConcoursController::class, 'detachCentre'])
        ->name('detach');
    });

    // --- Relationships: Filières ---
    Route::prefix('{concours}/filieres')->name('filieres.')->group(function () {
      Route::get('/disponibles', [ConcoursController::class, 'listFilieresDisponibles'])
        ->name('disponibles');

      Route::get('/', [ConcoursController::class, 'listFilieresAttachees'])
        ->name('index');

      Route::post('/', [ConcoursController::class, 'attachFiliere'])
        ->name('attach');

      Route::patch('/{filiere}', [ConcoursController::class, 'updateFiliereNombrePlaces'])
        ->name('update');

      Route::delete('/{filiere}', [ConcoursController::class, 'detachFiliere'])
        ->name('detach');
    });

    // --- Specialties (Specs) ---
    require __DIR__ . '/concours/specs.php';
  });
