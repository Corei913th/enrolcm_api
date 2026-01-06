<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Test\ReceiptTestController;
use App\Http\Controllers\OCR\OcrTestController;


// Routes pour les admins
Route::prefix('admin')->group(function () {
    Route::get('/receipts', [ReceiptTestController::class, 'index'])
        ->name('admin.receipts.index');

    Route::get('/receipts/pending', [ReceiptTestController::class, 'pending'])
        ->name('admin.receipts.pending');

    Route::get('/receipts/{receipt}', [ReceiptTestController::class, 'show'])
        ->name('admin.receipts.show');

    Route::post('/receipts/{receipt}/verify', [ReceiptTestController::class, 'verify'])
        ->name('admin.receipts.verify');
});


Route::post('/ocr/test', [OcrTestController::class, 'testOcr'])
    ->name('ocr.test');


Route::get('/ocr/status', function () {
    return response()->json(\App\Http\Controllers\OCR\OcrTestController::checkPdfSupport());
})->name('ocr.status');
