<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OCR\OcrTestController;

/**
 * Routes Admin - Gestion des Reçus (OCR)
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Test OCR
Route::post('ocr/test', [OcrTestController::class, 'testOcr']);
Route::get('ocr/status', function () {
  return response()->json(\App\Http\Controllers\OCR\OcrTestController::checkPdfSupport());
});
