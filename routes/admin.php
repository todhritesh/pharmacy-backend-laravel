<?php

use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\ManagePharmacist;
use App\Http\Controllers\admin\ManageStaff;
use App\Http\Controllers\admin\StatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('adminMiddleware')->group(function(){
    Route::prefix("manage-staff")->name('staff')->controller(ManageStaff::class)->group(function(){
        Route::get("/{id?}","getStaff")->name('get');
        Route::post("/","addStaff")->name('add');
        Route::put("/{id?}","editStaff")->name('edit');
        Route::put("verify/{id?}","verifyStaff")->name('verify');
    });

    Route::prefix("manage-pharmacist")->name('pharmacist')->controller(ManagePharmacist::class)->group(function(){
        Route::put("/{id?}","editPharmacist")->name('edit');
        Route::get("/{id?}","getPharmacist")->name('get');
        Route::post("/","addPharmacist")->name('add');
        Route::put("verify/{id?}","verifyPharmacist")->name('verify');
    });

    Route::get('/dashboard',[AdminDashboardController::class,"adminDashboard"])->name('adminDashboard');


    Route::controller(StatsController::class)->prefix('stats')->group(function(){
        Route::get('/monthly/sell','monthlySell');
    });

});

