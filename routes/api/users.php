<?php

use App\Http\Controllers\Admin\StaffController;
use Illuminate\Support\Facades\Route;



Route::post('', [StaffController::class, 'store']);



