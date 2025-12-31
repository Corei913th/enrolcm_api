<?php


use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
  require __DIR__ . '/api/departements.php';
});
