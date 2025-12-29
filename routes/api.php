<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Middleware désactivé temporairement pour les tests
Route::prefix('v1')->group(function () {
    require __DIR__.'/api/ecoles.php';
    require __DIR__.'/api/matieres.php';
});
