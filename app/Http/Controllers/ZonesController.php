<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZonesController extends Controller
{


    public function destroyDnsRecord($zone_id,$record_id)
    {


        $apiToken = config('services.cloudflare.api_token');
        $url = config('services.cloudflare.api_url') . "/zones/$zone_id/dns_records/$record_id";

        $response = Http::withHeaders([
            'authorization' => 'Bearer ' . $apiToken,
            'accept' => 'application/json'
        ])->delete($url);

        /** @var \Illuminate\Http\Client\Response $response */
        if ($response->successful()) {
            return redirect()->back()->with('success', 'Registro DNS eliminado correctamente de Cloudflare.');
        }

        return redirect()->back()->with('error', 'No se pudo eliminar el registro. Error: ' . $response->json()['errors'][0]['message']);
    }

    public function DnsRecordsRemoto($id){

        $zona = Zone::find($id);

        if(!$zona){
            return redirect()->route('zonas-lista');
        }

        
        $apiToken = config('services.cloudflare.api_token');
        $url = config('services.cloudflare.api_url') . "/zones"."/".$zona->zone_id."/dns_records";

        $response = Http::withHeaders([
            'authorization' => 'Bearer ' . $apiToken,
            'accept' => 'application/json'
        ])->get($url);

        /** @var \Illuminate\Http\Client\Response $response */
        if ($response->successful()) {
            $data = $response->json();
            $allRecords = $data['result'] ?? [];

            $records = collect($allRecords)->filter(function ($record) {
                return $record['type'] === 'A';
            })->values()->all(); // values() resetea los índices del array

            return view('admin.zones.dns-records', [
                'dnsRecords' => $records,
                'totalDnsRecords' => count($records),
                'zonaId' => $zona->zone_id
            ]);

            return view('admin.zones.dns-records', [
                'dnsRecords' => $records,
                'totalDnsRecords' => count($records),
                'zonaId'=> $zona->zone_id
            ]);
        }
        return view('admin.zones.dns-records', [
            'dnsRecords' => [],
            'totalDnsRecords' => 0,
            'zone_id'=> ''
        ]);
    }


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
        return view('admin.zones.crear', [
            'zonas' => [],
            'totalZonas' => 0
        ]);
    }
}
