<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;






Route::view('/login','auth.login')->name('login')->middleware('guest');

Route::post('/login',[AuthController::class,'authenticate'])->name('auth_login');

Route::middleware('auth')->group(function () {
    Route::get('/',[ViewController::class,'index'])->name('home');
    Route::get('logout',[AuthController::class,'logout'])->name('logout');


    Route::prefix('sites')->group(function () {
        Route::get('all',[SiteController::class,'index'])->name('v_sites');
        Route::get('create',[SiteController::class,'create'])->name('v_sites_create');
        Route::get('edit/{id}',[SiteController::class,'edit'])->name('v_sites_edit');
        Route::post('store/{id?}',[SiteController::class,'store'])->name('site_store');
        Route::post('destroy/{id?}',[SiteController::class,'destroy'])->name('site_destroy');
    });




});
