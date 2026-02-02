<?php

use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Admin - Gestion des Utilisateurs (Staff)
 * Middleware appliqué : auth:sanctum + role:ADMIN
 */

Route::get('/', [UserController::class, 'index']);
Route::post('/', [UserController::class, 'store']);
Route::get('{user}', [UserController::class, 'show']);
Route::put('{user}', [UserController::class, 'update']);
Route::delete('{user}', [UserController::class, 'destroy']);
