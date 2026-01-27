<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZonesController extends Controller
{
    public function traer(){
        $apiToken = config('services.cloudflare.api_token');
        $url = config('services.cloudflare.api_url') . "/zones";

        $response = Http::withHeaders([
            'authorization' => 'Bearer ' . $apiToken,
            'accept' => 'application/json'
        ])->get($url);
        $response->json();

        if ($response->successful()) {
            $data = $response->json();
            $zonas = $data['result'] ?? [];

            // Ordenar por nombre
            usort($zonas, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            return view('admin.dominios.crear', [
                'zonas' => $zonas,
                'totalZonas' => count($zonas)
            ]);
        }
    }
}
