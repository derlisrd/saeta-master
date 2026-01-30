<?php

namespace App\Http\Controllers;

use App\Models\Dominio;
use App\Models\Repositorio;
use App\Models\VM;
use App\Models\Zone;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Contamos los recursos
        $stats = [
            'repos' => Repositorio::count(),
            'vms'   => VM::count(),
            'zonas' => Zone::count(),
            'dominios'=> Dominio::count()
        ];

        // Si falta alguno, mostramos la vista de onboarding
        $needsOnboarding = ($stats['repos'] === 0 || $stats['vms'] === 0 || $stats['zonas'] === 0 || $stats['dominios']);

        return view('admin.index', compact('stats', 'needsOnboarding'));
    }
}
