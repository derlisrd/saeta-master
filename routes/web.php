<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;


Route::get('/login',[AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login',[AdminAuthController::class, 'login'])->name('admin.login.submit');


Route::group(['middleware' => ['auth:web']], function () {
    Route::get('admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

});