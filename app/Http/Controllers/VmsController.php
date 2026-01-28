<?php

namespace App\Http\Controllers;

use App\Models\VM;
use Illuminate\Http\Request;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

class VmsController extends Controller
{
    public function lista()
    {
        $vms = VM::all();
        return view('admin.vms.lista', compact('vms'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'ip' => 'required|ip',
            'ssh_key_file' => 'required|file', // Validamos que subió un archivo
        ]);

        // Leer el contenido del archivo subido
        $llaveContenido = file_get_contents($request->file('ssh_key_file')->getRealPath());

        $vm = new VM();
        $vm->nombre = $request->nombre;
        $vm->ip = $request->ip;
        $vm->usuario = $request->usuario ?? 'root';
        $vm->puerto = $request->puerto ?? 22;
        $vm->ssh_key = $llaveContenido; // Se guarda encriptado por el cast del modelo
        $vm->save();




        return redirect()->route('vms-lista')->with('success', 'Servidor registrado con éxito.');
    }


    
    public function formulario(){

        $vms = VM::all();

        return view('admin.vms.crear',['vms'=>$vms]);
    }


    public function destroy($id)
    {
        $vm = VM::findOrFail($id);

        // Verificamos si tiene dominios asociados
        if ($vm->dominios()->count() > 0) {
            return redirect()->back()->with(
                'warning',
                "No se puede eliminar el servidor {$vm->nombre} porque tiene instancias de dominios activas. Elimina los dominios primero."
            );
        }

        $vm->delete();

        return redirect()->route('vms-lista')->with('success', 'Servidor eliminado correctamente del inventario.');
    }


    public function test($id)
    {
        $vm = VM::findOrFail($id);

        try {
            // 1. Configurar la conexión
            $ssh = new SSH2($vm->ip, $vm->puerto);

            // 2. Cargar la llave (Laravel la descifra automáticamente si usaste 'encrypted' en el cast)
            $key = PublicKeyLoader::load($vm->ssh_key);

            // 3. Intentar Login
            if (!$ssh->login($vm->usuario, $key)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Autenticación fallida. ¿Añadiste la llave pública a authorized_keys?'
                ]);
            }

            // 4. Ejecutar un comando simple para confirmar
            $uptime = $ssh->exec('uptime -p');

            return response()->json([
                'success' => true,
                'message' => '¡Conexión exitosa! El servidor está: ' . $uptime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo conectar: ' . $e->getMessage()
            ]);
        }
    }



    public function find($id){

    }


    public function executeConsole(Request $request, VM $vm)
    {
        $request->validate(['command' => 'required|string']);

        // Evitar comandos extremadamente peligrosos por accidente
        $prohibidos = ['rm -rf /', 'mkfs', 'shutdown', 'reboot'];
        foreach ($prohibidos as $p) {
            if (str_contains($request->command, $p)) {
                return response()->json(['output' => 'Error: Comando prohibido por seguridad.']);
            }
        }

        $ssh = new \phpseclib3\Net\SSH2($vm->ip, $vm->puerto);
        $key = \phpseclib3\Crypt\PublicKeyLoader::load(file_get_contents(storage_path('app/ssh/id_rsa')));

        if ($ssh->login($vm->usuario, $key)) {
            // Ejecutamos el comando y capturamos la salida
            $output = $ssh->exec($request->command);
            return response()->json(['output' => $output ?: 'Comando ejecutado (sin salida).']);
        }

        return response()->json(['output' => 'Error: No se pudo conectar por SSH.'], 500);
    }
}
