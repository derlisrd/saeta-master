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
        $vm = VM::findOrFail($request->vm_id);
        $repo = Repositorio::findOrFail($request->repositorio_id);

        $prefix = Str::snake($request->subdominio . '_' . $repo->nombre);
        $dbUser = Str::limit($prefix . '_user', 63, ''); // Postgres limita a 63 caracteres
        $dbName = Str::limit($prefix . '_db', 63, '');
        $dbPass = Str::random(16); // Contraseña segura de 16 caracteres

        $request->merge([
            'ip' => $vm->ip,
            'bd_user' => $dbUser,
            'bd_name' => $dbName,
            'bd_pass' => $dbPass
        ]);
        // 2. Inyectamos la IP de la VM en el request para que pase la validación
        $request->merge(['ip' => $vm->ip]);

        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'vm_id'       => 'required|exists:vms,id', // Validamos que el ID de la VM sea real
            'nombre'      => 'required|string|max:255|unique:dominios,nombre',
            'repositorio_id' => 'required|exists:repositorios,id',
            'subdominio'  => 'required|string|max:255',
            'zone_id'     => 'required|exists:zones,zone_id',
            'protocolo'   => 'required|string',
            'ip'          => 'required|ip', // Ahora pasará porque la inyectamos arriba
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
            DesplegarProyectoJob::dispatch($dominio);
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
