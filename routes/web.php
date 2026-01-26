<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;


Route::get('/login',[AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login',[AdminAuthController::class, 'login'])->name('admin.login.submit');