<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DominioController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');


Route::group(['middleware' => ['auth:web']], function () {

    Route::prefix('/admin')->group(function () {
        Route::view('/','admin.index')->name('admin-index');
        
        Route::view('/dominios','admin.dominios.lista')->name('dominios-lista');
        Route::get('/dominios/crear',[DominioController::class,'crearDominioFormulario'])->name('dominios-crear');
        Route::post('/dominios/crear',[DominioController::class, 'crearDominio'])->name('dominios-store');
    });


    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
