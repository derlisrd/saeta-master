<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareService
{
    protected $apiToken;
    protected $baseUrl = 'https://api.cloudflare.com/client/v4';

    public function __construct()
    {
        $this->apiToken = config('services.cloudflare.api_token');
    }

    /**
     * Obtener Zone ID por nombre de dominio
     */
    public function getZoneId($domain)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/zones', [
            'name' => $domain
        ]);

        if ($response->successful && count($response->json['result']) > 0) {
            return $response->json['result'][0]['id'];
        }

        throw new \Exception("No se pudo encontrar el dominio {$domain} en Cloudflare");
    }

    /**
     * Crear registro DNS
     */
    public function createDNSRecord($domain, $subdomain, $ip, $type = 'A')
    {
        $zoneId = $this->getZoneId($domain);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . "/zones/{$zoneId}/dns_records", [
            'type' => $type,
            'name' => $subdomain, // solo 'pole' (Cloudflare agregará .saeta.app)
            'content' => $ip,
            'ttl' => 1, // Auto
            'proxied' => false, // true si quieres proxy de Cloudflare
        ]);

        if ($response->successful) {
            Log::info("DNS creado: {$subdomain}.{$domain} -> {$ip}");
            return $response->json['result'];
        }

        throw new \Exception("Error al crear DNS en Cloudflare: " . $response->body);
    }

    /**
     * Eliminar registro DNS
     */
    public function deleteDNSRecord($domain, $dnsId)
    {
        $zoneId = $this->getZoneId($domain);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
        ])->delete($this->baseUrl . "/zones/{$zoneId}/dns_records/{$dnsId}");

        return $response->successful ? true : false;
    }
}
