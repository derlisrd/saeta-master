<?php

namespace App\Jobs;

use App\Models\VM;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use Illuminate\Support\Facades\Log;

class TestSSHConnection implements ShouldQueue
{
    public static function check(VM $vm)
    {
        try {
            $ssh = new SSH2($vm->ip, $vm->puerto);
            $keyPath = storage_path('app/ssh/id_rsa');

            if (!file_exists($keyPath)) {
                return ['success' => false, 'message' => 'Llave privada no encontrada en el servidor local.'];
            }

            $key = PublicKeyLoader::load(file_get_contents($keyPath));

            if ($ssh->login($vm->usuario, $key)) {
                return ['success' => true, 'message' => 'Conexión exitosa.'];
            }

            return ['success' => false, 'message' => 'Error de autenticación (Llaves no autorizadas).'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Servidor inalcanzable: ' . $e->getMessage()];
        }
    }
}
