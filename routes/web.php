<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;






Route::view('/login','auth.login')->name('login')->middleware('guest');

Route::post('/login',[AuthController::class,'authenticate'])->name('auth_login');

Route::middleware('auth')->group(function () {
    Route::get('/home',[ViewController::class,'index'])->name('home');

});
