<?php

namespace App\Http\Controllers;

use App\Models\Dominio;
use App\Services\CloudflareService;
use App\Services\VPSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function crearDominio(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
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

        DB::beginTransaction();

        try {
            // 1. Crear registro en la base de datos
            $dominio = Dominio::create([
                'cliente_id' => $request->cliente_id,
                'nombre' => $request->nombre,
                'subdominio' => $request->subdominio,
                'dominio' => $request->dominio,
                'ip' => $request->ip,
                'type' => $request->type ?? 'A',
                'principal' => $request->principal ?? false,
                'premium' => $request->premium ?? false,
                'vencimiento' => $request->vencimiento,
            ]);

            $fullDomain = $request->subdominio . '.' . $request->dominio; // pole.saeta.app

            // 2. Crear DNS en Cloudflare
            $dnsRecord = $this->cloudflare->createDNSRecord(
                $request->dominio,
                $request->subdominio,
                $request->ip,
                $request->type ?? 'A'
            );

            // Actualizar el dominio con el DNS ID de Cloudflare
            $dominio->update(['dns' => $dnsRecord['id']]);

            // 3. Configurar VPS
            $this->vps->setupDomain([
                'subdominio' => $request->subdominio,
                'full_domain' => $fullDomain,
                'vps_host' => $request->vps_host,
                'vps_user' => $request->vps_user,
                'vps_password' => $request->vps_password,
                'repo_core' => $request->repo_core,
                'repo_admin' => $request->repo_admin,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dominio creado exitosamente',
                'data' => $dominio,
                'domain' => $fullDomain
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear dominio: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el dominio',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
