<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PreRegistrationController;


Route::post('pre-register/upload-receipt', [PreRegistrationController::class, 'uploadReceipt']);
Route::post('pre-register/manual-receipt', [PreRegistrationController::class, 'manualReceiptEntry']);
Route::post('pre-register/check-receipt', [PreRegistrationController::class, 'checkReceiptNumber']);


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
});
