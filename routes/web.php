<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\DominioController;
use App\Http\Controllers\ZonesController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');


Route::group(['middleware' => ['auth:web']], function () {

    Route::prefix('/admin')->group(function () {
        Route::view('/','admin.index')->name('admin-index');
        
        Route::get('/dominios',[DominioController::class,'lista'])->name('dominios-lista');
        Route::get('/dominios/crear',[DominioController::class,'formulario'])->name('dominios-crear');
        Route::post('/dominios/crear',[DominioController::class, 'store'])->name('dominios-store');


        Route::get('/zonas',[ZonesController::class,'lista'])->name('zonas-lista');
        Route::get('/zonas/crear',[ZonesController::class, 'formulario'])->name('zonas-crear');
        Route::post('/zonas/crear',[ZonesController::class, 'store'])->name('zonas-store');


        Route::get('/clientes',[ClientesController::class,'lista'])->name('clientes-lista');
        Route::get('/clientes/crear',[ClientesController::class, 'formulario'])->name('clientes-crear');
        Route::post('/clientes/crear',[ClientesController::class, 'store'])->name('clientes-store');
        Route::get('/clientes/{id}',[ClientesController::class, 'find'])->name('clientes-detalle');
    });


    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});
