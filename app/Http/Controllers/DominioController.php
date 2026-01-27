<?php

namespace App\Http\Controllers;

use App\Models\Dominio;
use App\Models\User;
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

    public function formulario(){

        $zonas = Zone::all();
        $clientes = User::orderBy('name')->get(); // Traemos todos los usuarios

        return view('admin.dominios.crear', [
            'zonas' => $zonas,
            'clientes' => $clientes
        ]);
        
    }


    public function lista(){

        return view('admin.dominios.lista',['dominios'=>Dominio::all()]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255|unique:dominios,nombre',
            'subdominio'  => 'required|string|max:255',
            'zone_id'     => 'required|exists:zones,zone_id', // Valida que el ID exista en tu tabla zones
            'protocolo'   => 'required|string',
            'ip'          => 'required|ip',
            'type'        => 'required|in:A,AAAA,CNAME',
            'vencimiento' => 'nullable|date',
        ]);

        // Creamos el dominio con todos los datos, incluyendo el zone_id
        $dominio = Dominio::create($request->all());

        return redirect()->route('dominios-lista')
            ->with('success', "Dominio {$dominio->nombre} vinculado correctamente a la zona.");
    }

    
}
