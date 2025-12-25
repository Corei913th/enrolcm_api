<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaymentReceiptController;

// Routes pour les candidats
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/receipts/upload', [PaymentReceiptController::class, 'upload'])
        ->name('receipts.upload');
    
    Route::get('/receipts/my-receipt', [PaymentReceiptController::class, 'myReceipt'])
        ->name('receipts.my-receipt');
});

// Routes pour les admins
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/receipts', [PaymentReceiptController::class, 'index'])
        ->name('admin.receipts.index');
    
    Route::get('/receipts/pending', [PaymentReceiptController::class, 'pending'])
        ->name('admin.receipts.pending');
    
    Route::get('/receipts/{receipt}', [PaymentReceiptController::class, 'show'])
        ->name('admin.receipts.show');
    
    Route::post('/receipts/{receipt}/verify', [PaymentReceiptController::class, 'verify'])
        ->name('admin.receipts.verify');
});
