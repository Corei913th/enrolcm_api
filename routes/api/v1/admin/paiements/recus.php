<?php

use App\Http\Controllers\OCR\OcrTestController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Gestion des Reçus (OCR)
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

// Test OCR
Route::post('ocr/test', [OcrTestController::class, 'testOcr']);
Route::get('ocr/status', function () {
    return response()->json(OcrTestController::checkPdfSupport());
});
