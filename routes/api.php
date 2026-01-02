<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    require __DIR__ . '/api/auth.php';
});

Route::prefix('payments')->group(function () {
    require __DIR__ . '/api/payments.php';
});


Route::prefix('receipts')->group(function () {
    require __DIR__ . '/api/receipts.php';
});
