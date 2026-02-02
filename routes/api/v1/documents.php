<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Documents\DocumentValidationController;

/**
 * ============================================
 * DOCUMENTS VALIDATION ROUTES - API V1
 * ============================================
 */

Route::prefix('admin/documents')
  ->middleware(['auth:sanctum', 'role:ADMIN'])
  ->name('admin.documents.')
  ->group(function () {

    Route::get('/pending', [DocumentValidationController::class, 'listPending'])
      ->name('pending');

    Route::get('/{document}', [DocumentValidationController::class, 'show'])
      ->name('show');

    Route::post('/{document}/validate', [DocumentValidationController::class, 'validateDocument'])
      ->name('validate');

    Route::post('/{document}/reject', [DocumentValidationController::class, 'reject'])
      ->name('reject');
  });
