<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DominioController;
use App\Http\Controllers\RepositorioController;
use App\Http\Controllers\VmsController;
use App\Http\Controllers\ZonesController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');



Route::group(['middleware' => ['auth:web']], function () {

    

    Route::prefix('/admin')->group(function () {
        Route::get('/',[DashboardController::class,'index'])->name('admin-index');
        
        Route::get('/dominios',[DominioController::class,'lista'])->name('dominios-lista');
        Route::get('/dominios/crear',[DominioController::class,'formulario'])->name('dominios-formulario');
        Route::post('/dominios/crear',[DominioController::class, 'store'])->name('dominios-store');

        Route::get('/dominios/{id}', [DominioController::class, 'find'])->name('dominios-detalle');
        Route::delete('/dominios/{id}', [DominioController::class, 'destroy'])->name('dominios-destroy');
        Route::post('/dominios/{id}/reintentar', [DominioController::class, 'reintentarDespliegue'])->name('dominios-reintentar');


        Route::get('/zonas',[ZonesController::class,'lista'])->name('zonas-lista');
        Route::get('/zonas/crear',[ZonesController::class, 'formulario'])->name('zonas-formulario');
        Route::post('/zonas/crear',[ZonesController::class, 'store'])->name('zonas-store');
        Route::get('/zonas/dns-records/{zonas_id}',[ZonesController::class, 'DnsRecordsRemoto'])->name('zonas-dns-records');
        Route::delete('/zonas/dns-records/{zone_id}/{record_id}', [ZonesController::class, 'destroyDnsRecord'])->name('zonas-dns-destroy');

        Route::get('/repositorios',[RepositorioController::class,'lista'])->name('repositorios-lista');
        Route::get('/repositorios/crear',[RepositorioController::class, 'formulario'])->name('repositorios-formulario');
        Route::post('/repositorios/crear',[RepositorioController::class, 'store'])->name('repositorios-store');


        Route::get('/repositorios/{id}', [RepositorioController::class, 'editar'])->name('repositorios-editar');
        Route::put('/repositorios/{id}', [RepositorioController::class, 'update'])->name('repositorios-update');
        Route::delete('/repositorios/{id}/eliminar', [RepositorioController::class, 'destroy'])->name('repositorios-destroy');
        
        Route::get('/github/branches', [RepositorioController::class, 'getBranches'])->name('github-branches');

        Route::get('/clientes',[ClientesController::class,'lista'])->name('clientes-lista');
        Route::get('/clientes/crear',[ClientesController::class, 'formulario'])->name('clientes-formulario');
        Route::post('/clientes/crear',[ClientesController::class, 'store'])->name('clientes-store');
        Route::get('/clientes/{id}',[ClientesController::class, 'find'])->name('clientes-detalle');


        Route::get('/vms',[VmsController::class,'lista'])->name('vms-lista');
        Route::get('/vms/crear',[VmsController::class, 'formulario'])->name('vms-formulario');
        Route::post('/vms/crear',[VmsController::class, 'store'])->name('vms-store');
        Route::get('/vms/{id}',[VmsController::class, 'find'])->name('vms-detalle');
        Route::get('/vms/{id}/test-ssh',[VmsController::class, 'test'])->name('vms-test');
        Route::delete('/vms/{id}/eliminar', [VmsController::class, 'destroy'])->name('vms-destroy');


        // routes/web.php
        Route::post('/vms/{vm}/console', [VmsController::class, 'executeConsole'])->name('vms-console');
    });


    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});
