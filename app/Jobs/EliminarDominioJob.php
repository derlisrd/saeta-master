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

    protected $dominio;
    protected $fullDomain;
    protected $path;
    protected $fullPath;
    protected $basePath;
    protected $repo;

    public function __construct(Dominio $dominio)
    {
        $this->dominio = $dominio;
        $this->fullDomain = $dominio->full_dominio;
        $this->repo = $dominio->repositorio;
        $this->basePath = rtrim($dominio->path, '/') . "/" . $this->fullDomain;
        $this->fullPath = $this->dominio->full_path;
    }

    public function handle(): void
    {
        $fullDomain = $this->fullDomain;
        $path = $this->basePath;
        $fullPath = $this->fullPath;


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

            Log::info("Dominio {$fullDomain} eliminado exitosamente del VPS.");
        } catch (\Throwable $th) {
            Log::error("Error crítico eliminando dominio {$fullDomain}: " . $th->getMessage());
            throw $th;
        }
    }
}
