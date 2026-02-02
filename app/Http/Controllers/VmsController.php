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
            'web_server_type'=> 'required|in:apache,nginx',
            'ssh_key_file' => 'required|file', // Validamos que subió un archivo
        ]);

        // Leer el contenido del archivo subido
        $llaveContenido = file_get_contents($request->file('ssh_key_file')->getRealPath());

        $vm = new VM();
        $vm->nombre = $request->nombre;
        $vm->ip = $request->ip;
        $vm->usuario = $request->usuario ?? 'root';
        $vm->web_server_type = $request->web_server_type;
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


    public function executeConsole(Request $request, $id)
    {
        // Buscamos la VM por ID
        $vm = VM::findOrFail($id);

        $request->validate(['command' => 'required|string']);

        // Seguridad básica
        $prohibidos = ['rm -rf /', 'mkfs', 'shutdown', 'reboot', 'dd '];
        foreach ($prohibidos as $p) {
            if (str_contains($request->command, $p)) {
                return response()->json(['output' => 'Error: Comando prohibido.'], 403);
            }
        }

        try {
            $ssh = new SSH2($vm->ip, (int)$vm->puerto);

            // CARGA LA LLAVE DESDE LA DB (ya descifrada por Laravel)
            $key = PublicKeyLoader::load($vm->ssh_key);

            if (!$ssh->login($vm->usuario, $key)) {
                return response()->json(['output' => 'Error: Autenticación fallida con el usuario ' . $vm->usuario], 401);
            }

            // Ejecutar comando
            $output = $ssh->exec($request->command);

            return response()->json([
                'success' => true,
                'output' => $output ?: 'Comando ejecutado (sin salida de consola).'
            ]);
        } catch (\Exception $e) {
            return response()->json(['output' => 'Error SSH: ' . $e->getMessage()], 500);
        }
    }
}
