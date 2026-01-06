<?php

use App\Http\Controllers\Test\ReceiptTestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/test/receipt-form', [ReceiptTestController::class, 'showForm'])
    ->name('test.receipt.form');

Route::match(['GET', 'POST'], '/test/generate-receipt', [ReceiptTestController::class, 'apiGenerateReceipt'])
    ->name('test.receipt.generate');
