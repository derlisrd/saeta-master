<?php

namespace App\Jobs;

use App\Models\Dominio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class EliminarDominioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Dominio $dominio) {}

    public function handle(): void
    {
        $domain = $this->dominio->dominio; // ej: mi-tienda.saeta.app
        $path = $this->dominio->path;      // ej: /var/www/html/mi-tienda
        $fullDomain = $this->dominio->subdominio . '.'. $this->dominio->dominio;
        $fullPath = '/var/www/html/'.$fullDomain;

        Log::info("Iniciando eliminación completa del dominio: {$fullDomain} con el path {$path}");

        try {
            $commands = [
                // 1. Eliminar configuración de Nginx
                "sudo rm -f /etc/nginx/sites-enabled/{$fullDomain}",
                "sudo rm -f /etc/nginx/sites-available/{$fullDomain}",

                // 2. Intentar eliminar certificados SSL (Certbot)
                "sudo certbot delete --cert-name {$fullDomain} --non-interactive",

                // 3. Reiniciar Nginx para aplicar cambios
                "sudo nginx -t && sudo systemctl reload nginx",

                // 4. Eliminar los archivos del proyecto (¡CUIDADO!)
                "sudo rm -rf {$fullPath}"
            ];

            foreach ($commands as $command) {
                //$result = Process::run($command);

                //if ($result->failed()) {
                    Log::warning("Comando hecho: {$command}." );
                //}
            }

            // 5. Eliminar de la base de datos
           $this->dominio->delete();

            Log::info("Dominio {$domain} eliminado exitosamente del VPS.");
        } catch (\Throwable $th) {
            Log::error("Error crítico eliminando dominio {$domain}: " . $th->getMessage());
            throw $th;
        }
    }
}
