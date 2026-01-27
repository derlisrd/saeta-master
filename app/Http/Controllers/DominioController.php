<?php

namespace App\Http\Controllers;

use App\Models\Dominio;
use App\Models\Zone;
use App\Services\CloudflareService;
use App\Services\VPSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DominioController extends Controller
{
    protected $cloudflare;
    protected $vps;
    public function __construct(CloudflareService $cloudflare, VPSService $vps)
    {
        $this->cloudflare = $cloudflare;
        $this->vps = $vps;
    }

    public function crearDominioFormulario(){
        
        $zonas = Zone::all();
        return view('admin.dominios.crear',['zonas'=>$zonas]);
        
    }






    public function crearDominio(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nombre' => 'required|string',
            'subdominio' => 'required|string|unique:dominios,subdominio',
            'dominio' => 'required|string',
            'ip' => 'required|ip',
            'type' => 'nullable|string|in:A,CNAME,AAAA',
            'principal' => 'nullable|boolean',
            'premium' => 'nullable|boolean',
            'vencimiento' => 'required|date',
            'vps_host' => 'required|string', // IP del VPS
            'vps_user' => 'required|string', // Usuario SSH
            'vps_password' => 'required|string', // Contraseña SSH
            'repo_core' => 'required|url', // Repositorio Laravel
            'repo_admin' => 'required|url', // Repositorio React
        ]);
    }
}
