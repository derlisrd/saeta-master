<?php

namespace App\Http\Controllers;

use App\Jobs\DesplegarProyectoJob;
use App\Models\Dominio;
use App\Models\Repositorio;
use App\Models\User;
use App\Models\VM;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DominioController extends Controller
{

    public function formulario(){

        return view('admin.dominios.crear', [
            'zonas'    => Zone::all(),
            'clientes' => User::all(),
            'vms'      => VM::orderBy('nombre')->get(),
            'repositorios' => Repositorio::orderBy('nombre')->get()
        ]);
        
    }


    public function lista(){

        return view('admin.dominios.lista',['dominios'=>Dominio::all()]);
    }



    public function store(Request $request)
    {
        // 1. Buscamos la VM para obtener la IP real
        $vm = VM::findOrFail($request->vm_id);

        // 2. Validación estricta
        $request->validate([
            'user_id'        => 'required|exists:users,id',
            'vm_id'          => 'required|exists:vms,id',
            'repositorio_id' => 'required|exists:repositorios,id',
            'nombre'         => 'required|string|max:255',
            'subdominio'     => 'required|string|max:255|unique:dominios,subdominio',
            'zone_id'        => 'required|exists:zones,id',
            'vencimiento'    => 'required|date',

            // Campos que vienen del Paso 2 del formulario
            'db_connection'  => 'required|in:pgsql,mysql,sqlite',
            'db_name'        => 'required|string',
            'db_user'        => 'required|string',
            'db_pass'        => 'required|string',
            'db_port'        => 'required|numeric',
            'api_key'        => 'required|string',
        ]);

        // 3. Preparar datos adicionales
        $zonaDB = Zone::where('id', $request->zone_id)->firstOrFail();

        $data = $request->all();
        $data['dominio'] = $zonaDB->dominio;
        $data['zone_id'] = $request->zone_id;
        $data['ip']      = $vm->ip; // Inyectamos la IP de la VM para Cloudflare y DB
        $data['protocol'] = 'https://'; // Por defecto

        // 4. Guardar en Base de Datos Local
        $dominio = Dominio::create($data);

        if ($request->has('custom_envs')) {
            foreach ($request->custom_envs as $env) {
                if (!empty($env['key'])) {
                    $dominio->envs()->create([
                        'key' => strtoupper($env['key']),
                        'value' => $env['value'] ?? '',
                    ]);
                }
            }
        }

        // 5. Sincronización con Cloudflare
        $apiToken = config('services.cloudflare.api_token');
        $cfUrl = "https://api.cloudflare.com/client/v4/zones/{$zonaDB->zone_id}/dns_records";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiToken,
            'Content-Type'  => 'application/json',
        ])->post($cfUrl, [
            'name'    => $request->subdominio,
            'type'    => 'A',
            'content' => $vm->ip,
            'ttl'     => 3600,
            'proxied' => false, // Nube naranja activada
            'comment' => 'Dominio creado desde el Panel Maestro'
        ]); 

        if ($response->successful()) {
            // 6. LANZAR EL JOB DE DESPLIEGUE SSH
            // Pasamos el objeto $dominio que ya tiene las credenciales de DB y API Key
            DesplegarProyectoJob::dispatch($dominio);

            return redirect()->route('dominios-lista')
                ->with('success', "Dominio registrado. El despliegue de la infraestructura ha comenzado.");
        } 

        // Si falla Cloudflare, registramos el error pero el dominio ya quedó en nuestra DB
        //Log::error('Error Cloudflare API', ['res' => $response->json()]);
        return redirect()->route('dominios-lista')
            ->with('warning', "Dominio guardado localmente, pero falló la sincronización DNS.");
    }

    


    public function reintentarDespliegue($id)
    {
        $dominio = Dominio::findOrFail($id);

        // Ponemos el estado en 0 mientras se procesa de nuevo
        $dominio->update(['desplegado' => false]);

        // Disparamos el Job de nuevo
        DesplegarProyectoJob::dispatch($dominio);

        return redirect()->back()->with('success', "Reintento de despliegue iniciado para {$dominio->subdominio}");
    }

    public function destroy($id)
    {
        $dominio = Dominio::findOrFail($id);

        // 1. Opcional: Podrías intentar borrar el registro DNS en Cloudflare aquí
        // Para simplificar, primero borramos el registro local.

        try {
            $nombre = $dominio->nombre;
            $fullDomain = "{$dominio->subdominio}.{$dominio->dominio}";

            $dominio->delete();

            return redirect()->route('dominios-lista')->with('success', "El dominio {$nombre} ({$fullDomain}) ha sido eliminado del panel.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "No se pudo eliminar el dominio: " . $e->getMessage());
        }
    }
    
    
}
