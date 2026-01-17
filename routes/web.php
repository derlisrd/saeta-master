<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;


Route::view('/','home');

Route::livewire('/login', 'pages::public.login');

Route::middleware(['auth'])->group(function () {

    Route::prefix('/auth')->group(function(){
        Route::livewire('/dash', 'pages::auth.dash');
        Route::livewire('/dominios', 'pages::auth.dominios');
    });
   
});