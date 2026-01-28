<?php

namespace App\Jobs;

use App\Models\{DbVms, Dominio};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use Illuminate\Support\Facades\Log;

class DesplegarProyectoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dominio;
    protected $fullDomain;
    protected $path;
    protected $basePath;

    public function __construct(Dominio $dominio)
    {
        $this->dominio = $dominio;
        // Definimos rutas base desde el inicio
        $this->fullDomain = "{$dominio->subdominio}.{$dominio->dominio}";
        $this->basePath = "/var/www/html/{$this->fullDomain}";
        $this->path = "{$this->basePath}/" . ltrim($dominio->path, '/');
    }

    public function handle()
    {
        $this->dominio->load(['repositorio', 'vm', 'user', 'envs']);

        if (!$this->dominio->repositorio) {
            Log::error("❌ Repositorio faltante para: {$this->dominio->nombre}");
            return;
        }

        try {
            $ssh = $this->getSshConnection();

            $commands = array_merge(
                $this->prepareDirectoryCommands(),
                $this->databaseCommands(),
                $this->deploymentCommands(),
                $this->serverConfigurationCommands()
            );

            foreach ($commands as $cmd) {
                Log::info("[$this->fullDomain] Ejecutando: $cmd");
                $ssh->exec($cmd);
            }

            $this->finalizeDeployment();
        } catch (\Exception $e) {
            Log::error("❌ Error desplegando {$this->fullDomain}: " . $e->getMessage());
        }
    }

    /** * SECCIONES DE COMANDOS 
     */

    private function prepareDirectoryCommands(): array
    {
        return [
            "sudo rm -rf {$this->path}",
            "sudo mkdir -p {$this->path}",
            "sudo chown {$this->dominio->vm->usuario}:{$this->dominio->vm->usuario} {$this->path}",
        ];
    }

    private function databaseCommands(): array
    {
        $d = $this->dominio;
        return [
            "sudo -u postgres psql -c \"CREATE USER {$d->db_user} WITH PASSWORD '{$d->db_pass}';\" || true",
            "sudo -u postgres psql -c \"CREATE DATABASE {$d->db_name} OWNER {$d->db_user};\" || true",
            "sudo -u postgres psql -c \"GRANT ALL PRIVILEGES ON DATABASE {$d->db_name} TO {$d->db_user};\"",
        ];
    }

    private function deploymentCommands(): array
    {
        $repo = $this->dominio->repositorio;
        return [
            "git clone -b {$repo->branch} {$repo->url_git} {$this->path}",
            "echo '{$this->getEnvContent()}' > {$this->path}/.env",
            "cd {$this->path} && composer install --no-dev --optimize-autoloader",
            "cd {$this->path} && php artisan key:generate && php artisan jwt:secret --force",
            "cd {$this->path} && php artisan migrate --seed --force",
        ];
    }

    private function serverConfigurationCommands(): array
    {
        $nginxCmd = "sudo bash -c \"cat << 'EOF' > /etc/nginx/sites-available/{$this->fullDomain}\n"
            . $this->getNginxConfig() .
            "\nEOF\n\"";


        return [
            $nginxCmd,
            "sudo ln -sf /etc/nginx/sites-available/{$this->fullDomain} /etc/nginx/sites-enabled/",
            "sudo nginx -t && sudo systemctl reload nginx", // Validar antes de Certbot
            "sudo certbot --nginx -d {$this->fullDomain} --non-interactive --agree-tos -m {$this->dominio->user->email} || true",
            "sudo systemctl reload nginx",
            "sudo chown -R www-data:www-data {$this->path}",
            "sudo chmod -R 775 {$this->path}/storage {$this->path}/bootstrap/cache",
        ];
    }

    /** * GENERADORES DE CONTENIDO 
     */

    private function getEnvContent(): string
    {
        $d = $this->dominio;
        // Agregamos comillas a los valores para evitar fallos si tienen espacios o caracteres especiales
        $env = "APP_NAME=\"{$d->nombre}\"\n";
        $env .= "APP_ENV=production\n";
        $env .= "APP_KEY=\n";
        $env .= "APP_DEBUG=false\n";
        $env .= "APP_URL=https://{$this->fullDomain}\n\n";

        $env .= "DB_CONNECTION={$d->db_connection}\n";
        $env .= "DB_HOST={$d->db_host}\n";
        $env .= "DB_PORT={$d->db_port}\n";
        $env .= "DB_DATABASE=\"{$d->db_name}\"\n";
        $env .= "DB_USERNAME=\"{$d->db_user}\"\n";
        $env .= "DB_PASSWORD=\"{$d->db_pass}\"\n\n";

        $env .= "X_API_KEY=\"{$d->api_key}\"\n";

        foreach ($d->envs as $custom) {
            $env .= "{$custom->key}=\"{$custom->value}\"\n";
        }
        return $env;
    }

    private function getNginxConfig(): string
    {
        return view('templates.nginx', [
            'domain' => $this->fullDomain,
            'path' => $this->path,
            'basePath' => $this->basePath
        ])->render();
    }

    /** * HELPERS 
     */

    private function getSshConnection(): SSH2
    {
        $vm = $this->dominio->vm;
        $ssh = new SSH2($vm->ip, $vm->puerto);
        $key = PublicKeyLoader::load($vm->ssh_key);
        if (!$ssh->login($vm->usuario, $key)) throw new \Exception("SSH Login Failed");
        $ssh->setTimeout(300);
        return $ssh;
    }

    private function finalizeDeployment()
    {
        DbVms::updateOrCreate(
            ['dominio_id' => $this->dominio->id],
            [
                'host' => '127.0.0.1',
                'port' => $this->dominio->db_port,
                'db_name' => $this->dominio->db_name,
                'db_user' => $this->dominio->db_user,
                'db_pass' => $this->dominio->db_pass,
                'db_connection' => $this->dominio->db_connection
            ]
        );

        $this->dominio->update(['desplegado' => true]);
        Log::info("✅ Proyecto listo: {$this->fullDomain}");
    }
}
