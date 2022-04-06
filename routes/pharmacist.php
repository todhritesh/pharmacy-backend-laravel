<?php

use App\Http\Controllers\pharmacist\ManageStocks;
use App\Http\Controllers\pharmacist\PharmacistDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('pharmacistMiddleware')->group(function (){
    Route::prefix("manage-stocks")->name('stocks')->controller(ManageStocks::class)->group(function(){
        // Route::get("/{id?}","getStocks")->name('get');
        Route::get("/get_expired_or_sellable_stocks/{status?}","get_expired_or_sellable_stocks")->name('get');
        Route::post("/","addStock")->name('add');
        Route::put("/{id?}","editStock")->name('edit');
        Route::delete("/{id?}","deleteStock")->name('delete');
    });

    Route::get("/dashboard",[PharmacistDashboardController::class,'pharmacistDashboard']);
});




