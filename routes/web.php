<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
Route::view('/','home');

Route::livewire('/login', 'pages::public.login')->mi;