<?php


use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    require __DIR__ . '/api/auth.php';
});



Route::middleware('auth:sanctum')->group(function () {
  
     Route::prefix('admin/users')->group(function () {
        require __DIR__ . '/api/users.php';
     });
    
});
