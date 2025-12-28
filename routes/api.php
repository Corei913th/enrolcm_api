<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Routes pour le module Écoles
    require __DIR__.'/api/ecoles.php';
});
