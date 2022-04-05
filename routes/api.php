<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::controller(AuthController::class)->group(function () {

    Route::post('login', 'login');
    Route::post('register', 'register');
    
    Route::middleware('auth:admin_api')->group(function () {
        Route::post('logout', 'logout');
        Route::post('refresh/token','refresh');
        Route::post('me', 'me');
    });

});
