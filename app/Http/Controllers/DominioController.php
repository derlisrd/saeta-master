<?php

namespace App\Http\Controllers;

use App\Models\Dominio;
use App\Models\User;
use App\Models\Zone;
use App\Services\CloudflareService;
use App\Services\VPSService;
use Illuminate\Http\Request;
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
            'user_id'     => 'required|exists:users,id',
            'nombre'      => 'required|string|max:255|unique:dominios,nombre',
            'subdominio'  => 'required|string|max:255',
            'zone_id'     => 'required|exists:zones,zone_id',
            'protocolo'   => 'required|string',
            'ip'          => 'required|ip',
            'type'        => 'required|in:A,AAAA,CNAME',
            'vencimiento' => 'required|date',
        ]);

        $zonaDB = Zone::where('zone_id', $request->zone_id)->firstOrFail();

        // 2. Preparamos los datos y agregamos manualmente el campo 'dominio'
        $data = $request->all();
        $data['dominio'] = $zonaDB->dominio; // Aquí llenamos lo que falta

        // 3. Ahora sí, creamos el registro local
        $dominio = Dominio::create($data);
        // 2. Configuración de Cloudflare
        $apiToken = config('services.cloudflare.api_token');

        $url = "https://api.cloudflare.com/client/v4/zones/{$request->zone_id}/dns_records";



        // 5. Ejecutar la petición (Igual al cURL)
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiToken,
            'Content-Type'  => 'application/json',
        ])->post($url, [
            'name'    => $request->subdominio,
            'type'    => $request->type,
            'content' => $request->ip,
            'ttl'     => 3600,
            'proxied' => false, // Cambiar a true si quieres el proxy (nube naranja)
            'comment' => 'Creado desde el panel Admin'
        ]);

        if ($response->successful()) {
            return redirect()->route('dominios-lista')
                ->with('success', "Dominio registrado y sincronizado con Cloudflare.");
        }

        // 2. Si falla, registramos un LOG detallado
        $errorData = $response->json();

        Log::error('Fallo en Sincronización Cloudflare', [
            'usuario_id'   => auth()->id() ?? 'Sistema',
            'zone_id'      => $request->zone_id,
            'subdominio'   => $request->subdominio,
            'status_code'  => $response->status(),
            'cf_response'  => $errorData, // Aquí guardamos todo lo que Cloudflare nos respondió
            'payload_sent' => [           // También guardamos qué fue lo que enviamos
                'name' => $request->subdominio,
                'type' => $request->type,
                'content' => $request->ip
            ]
        ]);

        // 3. Informar al usuario
        $mensaje = $errorData['errors'][0]['message'] ?? 'Error desconocido en la API';
        return redirect()->route('dominios-lista')
            ->with('warning', "Guardado local, pero Cloudflare falló. Revisa los logs: " . $mensaje);

       
    }
    
}
