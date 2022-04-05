<?php

use App\Http\Controllers\staff\SellController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('staffMiddleware')->group(function (){
    Route::prefix("sell")->controller(SellController::class)->group(function(){
        Route::post("/{id?}/{cus_id?}","sellStock")->name('sell');
    });
    Route::get("/dashboard",[SellController::class,"staffDashboard"])->name('staff.dashboard');
    Route::get("/available/stocks",[SellController::class,"availableStocks"])->name('staff.availableStocks');
    Route::get("/autocomplete/data",[SellController::class,"autocompleteData"])->name('staff.autocompleteData');
    Route::post("/checkuser/{status?}",[SellController::class,"checkUser"])->name('staff.checkUser');
});

