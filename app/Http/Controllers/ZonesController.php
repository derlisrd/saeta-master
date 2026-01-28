<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZonesController extends Controller
{

    public function lista(){
        return view('admin.zones.lista', ['zones' => Zone::all()]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'zona_seleccionada' => 'required|string',
        ]);

        // Separamos el ID y el Nombre que vienen en el value del select
        // Ejemplo: "12345abc|misitio.com"
        $partes = explode('|', $request->zona_seleccionada);
        $zoneIdCloudflare = $partes[0];
        $nombreDominio = $partes[1];

        // Lógica de Sincronización: Actualiza si existe el zone_id, sino lo crea.
        $zona = Zone::updateOrCreate(
            ['zone_id' => $zoneIdCloudflare], // Condición de búsqueda
            ['dominio' => $nombreDominio]     // Datos a actualizar o insertar
        );

        return redirect()->route('zonas-lista')
            ->with('success', "Zona {$nombreDominio} sincronizada correctamente.");
    }


    public function formulario(){
        $apiToken = config('services.cloudflare.api_token');
        $url = config('services.cloudflare.api_url') . "/zones";

        $response = Http::withHeaders([
            'authorization' => 'Bearer ' . $apiToken,
            'accept' => 'application/json'
        ])->get($url);
        
        /** @var \Illuminate\Http\Client\Response $response */
        if ($response->successful()) {
            $data = $response->json();
            $zonas = $data['result'] ?? [];

            // Ordenar por nombre
            usort($zonas, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            return view('admin.zones.crear', [
                'zonas' => $zonas,
                'totalZonas' => count($zonas)
            ]);
        }
    }
}
