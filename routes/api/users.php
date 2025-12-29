<?php

use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;


// Routes admin pour gestion des utilisateurs
Route::post('/', [UserController::class, 'store']); // Créer un utilisateur (membre du staff)
