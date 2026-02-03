<?php

namespace App\Jobs;

use App\Models\{DbVms, Dominio, ServerTemplate};
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
    protected $fullPath;
    protected $basePath;
    protected $repo;

    public function __construct(Dominio $dominio)
    {
        $this->dominio = $dominio;
        // Definimos rutas base desde el inicio
        //$this->fullDomain = $dominio->subdominio? "{$dominio->subdominio}.{$dominio->dominio}" : $dominio->dominio;
        $this->fullDomain = $dominio->full_dominio;
        $this->repo = $dominio->repositorio;
        $this->basePath = rtrim($dominio->path, '/') . "/" . $this->fullDomain;
        $this->fullPath = $this->dominio->full_path; // "{$this->basePath}/" . ltrim($dominio->path, '/');
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

            if (env('APP_ENV') === 'local') {
                foreach ($commands as $cmd) {
                    Log::info($cmd);
                }
                $this->finalizeDeployment();
                return;
            }
            foreach ($commands as $cmd) {
                Log::info($cmd);
                $output = $ssh->exec($cmd);
                $exitCode = $ssh->getExitStatus();

                if ($exitCode !== 0) {
                    Log::error("Command failed: $cmd", ['output' => $output, 'code' => $exitCode]);
                    throw new \Exception("Fallo comando: $cmd");
                } 
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
        if (empty($this->fullPath) || $this->fullPath === '/') {
            throw new \Exception("Path inválido para eliminar");
        }

        return [
            "sudo rm -rf {$this->fullPath}",
            "sudo mkdir -p {$this->fullPath}",
            "sudo chown {$this->dominio->vm->usuario}:{$this->dominio->vm->usuario} {$this->fullPath}",
        ];
    }

    private function databaseCommands(): array
    {
        $d = $this->dominio;
        $pass = str_replace("'", "''", $d->db_pass);

        return [
            "sudo -u postgres psql -c \"CREATE USER {$d->db_user} WITH PASSWORD '{$pass}';\" || true",
            "sudo -u postgres psql -c \"CREATE DATABASE {$d->db_name} OWNER {$d->db_user};\" || true",
            "sudo -u postgres psql -c \"GRANT ALL PRIVILEGES ON DATABASE {$d->db_name} TO {$d->db_user};\"",
        ];
    }

    private function deploymentCommands(): array
    {
        $repo = $this->repo;
        $envEncoded = base64_encode($this->getEnvContent());

        $rawUrl = trim($repo->url_git);
        $token = config('services.github.token');

        $secureUrl = str_replace('https://', "https://x-access-token:{$token}@", $rawUrl);

        $cmds = [
            "git clone -b {$repo->branch} '{$secureUrl}' {$this->fullPath}",
            "echo '{$envEncoded}' | base64 -d > {$this->fullPath}/.env",
            //"cd {$this->fullPath} && composer install --no-dev --optimize-autoloader",
            //"cd {$this->fullPath} && php artisan key:generate && php artisan jwt:secret --force",
            //"cd {$this->fullPath} && php artisan migrate --seed --force",
        ];

        $fases = [
            'install' => $repo->install_commands,
            'build'   => $repo->build_commands,
            'setup'   => $repo->setup_commands,
        ];

        foreach ($fases as $fase => $contenido) {
            if (!empty($contenido)) {
                // Convertimos el texto en un array de líneas
                $lineas = explode("\n", str_replace("\r", "", $contenido));
                foreach ($lineas as $linea) {
                    $linea = trim($linea);
                    if ($linea) {
                        // Cada comando se ejecuta dentro de la carpeta del proyecto
                        $cmds[] = "cd {$this->fullPath} && {$linea}";
                    }
                }
            }
        }

        

        $cmds[] = "cd {$this->path} && git remote set-url origin {$repo->url_git}";

        return $cmds;
    }

    private function serverConfigurationCommandsAntiguo(): array
    {
        $nginxCmd = "sudo bash -c \"cat << 'EOF' > /etc/nginx/sites-available/{$this->fullDomain}\n"
            . $this->getNginxConfig() .
            "\nEOF\n\"";


        return [
            $nginxCmd,
            "sudo ln -s /etc/nginx/sites-available/{$this->fullDomain} /etc/nginx/sites-enabled/",
            "sudo nginx -t && sudo systemctl reload nginx", // Validar antes de Certbot
            "sudo certbot --nginx -d " . $this->fullDomain . " --non-interactive --agree-tos -m " . $this->dominio->user->email . " --redirect",

            "sudo systemctl reload nginx",
            "sudo chown -R " . $this->dominio->vm->usuario . ":www-data {$this->fullPath}",
            "sudo chmod -R 775 {$this->fullPath}/storage {$this->fullPath}/bootstrap/cache",
        ];
    }

    private function serverConfigurationCommands(): array
    {
        $vm = $this->dominio->vm;
        $webServer = $vm->web_server_type; // 'nginx' o 'apache'

        // Obtenemos el contenido de la plantilla dinámica
        $configContent = $this->getDynamicServerConfig();

        if ($webServer === 'nginx') {
            $availablePath = "/etc/nginx/sites-available/{$this->fullDomain}";
            $enabledPath = "/etc/nginx/sites-enabled/{$this->fullDomain}";

            $cmds = [
                "sudo bash -c \"echo '$configContent' > $availablePath\"",
                "sudo ln -sf $availablePath $enabledPath",
                "sudo nginx -t && sudo systemctl reload nginx",
            ];
        } else {
            // Lógica para Apache
            $confFile = "/etc/apache2/sites-available/{$this->fullDomain}.conf";
            $cmds = [
                "sudo bash -c \"echo '$configContent' > $confFile\"",
                "sudo a2ensite {$this->fullDomain}.conf",
                "sudo systemctl reload apache2",
            ];
        }

        // SSL y Permisos (Comunes o adaptados)
        $cmds[] = "sudo certbot --" . ($webServer === 'apache' ? 'apache' : 'nginx') . " -d {$this->fullDomain} --non-interactive --agree-tos -m {$this->dominio->user->email} --redirect";
        $cmds[] = "sudo chown -R {$vm->usuario}:www-data {$this->fullPath}";
        $cmds[] = "sudo chmod -R 775 {$this->fullPath}/storage {$this->fullPath}/bootstrap/cache";

        return $cmds;
    }

    private function getDynamicServerConfig(): string
    {
        $vm = $this->dominio->vm;
        $repo = $this->dominio->repositorio;

        // Buscamos la plantilla que coincida con el motor de la VM y el Stack del Repo
        $template = ServerTemplate::where('web_server', $vm->web_server_type)
            ->where('stack_id', $repo->stack_id)
            ->first();

        if (!$template) {
            throw new \Exception("No existe una plantilla para {$vm->web_server_type} con el stack {$repo->stack->nombre}");
        }

        $outputPath = $this->fullPath . '/' . ltrim($repo->output_path, '/');

        // Mapeo de variables para la plantilla
        $vars = [
            '{{dominio}}'      => $this->fullDomain,
            '{{path}}'         => $this->fullPath,
            '{{output_path}}'  => $outputPath,
            '{{php_version}}'  => $vm->php_version,
        ];

        return str_replace(array_keys($vars), array_values($vars), $template->config_content);
    }



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
        $domain = $this->fullDomain;
        //$basePath = $this->basePath;
        //$publicPath = $this->path . '/public';
        $fullPath = $this->fullPath;
        $outputPath =  $this->fullPath .'/'. $this->repo->output_path;

        return "
server {
    listen 80;
    listen [::]:80;
    server_name {$domain};
    root {$outputPath};

    add_header X-Frame-Options ".'"SAMEORIGIN"'. ";
    add_header X-Content-Type-Options ".'"nosniff"'.";

    index index.php;
    charset utf-8;

    error_page 404 /index.php;
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ ^/index\\.php(/|$) {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        ffastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }


    location ~ \\.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}";
    }
    /** * HELPERS 
     */

    private function getSshConnection(): SSH2
    {
        $vm = $this->dominio->vm;
        $ssh = new SSH2($vm->ip, $vm->puerto);
        $key = PublicKeyLoader::load($vm->ssh_key);
        if (!$ssh->login($vm->usuario, $key)) throw new \Exception("SSH Login Failed");
        $ssh->setTimeout(600);
        return $ssh;
    }

    private function finalizeDeployment()
    {
        DbVms::create([
                'dominio_id' => $this->dominio->id,
                'db_host' => $this->dominio->db_host,
                'db_port' => $this->dominio->db_port,
                'db_name' => $this->dominio->db_name,
                'db_user' => $this->dominio->db_user,
                'db_pass' => $this->dominio->db_pass,
                'db_connection' => $this->dominio->db_connection
            ]);

        $this->dominio->update(['desplegado' => 1]);
        Log::info("✅ Proyecto listo: {$this->fullDomain}");
    }




    private function generateWebConfig($dominio): string
    {
        // Supongamos que el dominio tiene un template_id asociado
        $template = ServerTemplate::find($dominio->template_id);

        $config = $template->config_skeleton;

        // Reemplazamos los placeholders por los datos reales del dominio
        $busqueda = ['{{dominio}}', '{{path}}', '{{public_path}}'];
        $reemplazo = [$dominio->subdominio . '.' . $dominio->zona->dominio, $dominio->path, $dominio->path . '/public'];

        return str_replace($busqueda, $reemplazo, $config);
    }

}
