<?php


namespace App\Jobs;

use App\Models\Dominio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use Illuminate\Support\Facades\Log;

class DesplegarProyectoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dominio;

    public function __construct(Dominio $dominio)
    {
        $this->dominio = $dominio;
    }

    public function handle()
    {
        $this->dominio->load(['repositorio', 'vm', 'user', 'envs']);

        $vm = $this->dominio->vm;
        $repo = $this->dominio->repositorio;
        $owner = $this->dominio->user;

        // VALIDACIÓN DE SEGURIDAD
        if (!$repo) {
            Log::error("❌ Error: El dominio {$this->dominio->nombre} no tiene un repositorio asignado.");
            return; // Detenemos el proceso para evitar el error de "null"
        }
        $vm = $this->dominio->vm;
        $repo = $this->dominio->repositorio;
        $fullDomain = "{$this->dominio->subdominio}.{$this->dominio->dominio}";
        $path = "/var/www/html/{$fullDomain}/" . $this->dominio->path;
        $basePath = "/var/www/html/{$fullDomain}";

        $nginxConfig = "
server {
    listen 80;
    server_name {$fullDomain};
    root {$basePath};

    # Configuración para /admin - React
    location /admin {
        alias {$basePath}/admin/dist/;
        index index.html;
        try_files \$uri \$uri/ /admin/index.html;
    }

    # Estáticos de React
    location ~ ^/admin/(.*\\.(js|css|png|jpg|jpeg|gif|svg|ico|json|woff|woff2|ttf|eot))$ {
        alias {$basePath}/admin/dist/\$1;
    }

    # Configuración para /v1 - Laravel API
    location /v1 {
        try_files \$uri \$uri/ /v1/index.php?\$query_string;
    }

    location ~ ^/v1/index\\.php(/|$) {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME {$path}/public/index.php;
        include fastcgi_params;
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}";

        $dbName = $this->dominio->db_name;
        $dbUser = $this->dominio->db_user;
        $dbPass = $this->dominio->db_pass;
        $dbPort = $this->dominio->db_port;
        $apiKey = $this->dominio->api_key;
        $dbConnection = $this->dominio->db_connection;


        try {
            $ssh = new SSH2($vm->ip, $vm->puerto);
            $key = PublicKeyLoader::load($vm->ssh_key);

            if (!$ssh->login($vm->usuario, $key)) {
                throw new \Exception("Login fallido en {$vm->ip}");
            }

            // Aumentar el tiempo de espera para comandos largos (como composer install)
            $ssh->setTimeout(300);

            $envContent = "APP_NAME={$this->dominio->nombre}
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://{$fullDomain}

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION={$dbConnection}
DB_HOST=127.0.0.1
DB_PORT={$dbPort}
DB_DATABASE={$dbName}
DB_USERNAME={$dbUser}
DB_PASSWORD={$dbPass}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
X_API_KEY={$apiKey}
URL_API_RUC_PARAGUAY=https://turuc.com.py/api/contribuyente
API_KEY_RUC_PARAGUAY=0

# Datos para el Seeder del nuevo proyecto
USER_SEED=\"{$owner->email}\"
EMAIL_SEED=\"{$owner->email}\"
PASSWORD_SEED=\"{$owner->password}\"
";

            if ($this->dominio->envs->count() > 0) {
                $envContent .= "\n# Custom Variables\n";
                foreach ($this->dominio->envs as $customEnv) {
                    $envContent .= "{$customEnv->key}=\"{$customEnv->value}\"\n";
                }
            }

            $commands = [
                // 1. Preparar Directorio
                "sudo rm -rf {$path}", // Limpiamos por si es un reintento
                "sudo mkdir -p {$path}",
                "sudo chown {$vm->usuario}:{$vm->usuario} {$path}",

                // 2. Base de Datos (Hacerlo ANTES de artisan migrate)
                "sudo -u postgres psql -c \"CREATE USER {$dbUser} WITH PASSWORD '{$dbPass}';\"",
                "sudo -u postgres psql -c \"CREATE DATABASE {$dbName} OWNER {$dbUser};\"",
                "sudo -u postgres psql -c \"GRANT ALL PRIVILEGES ON DATABASE {$dbName} TO {$dbUser};\"",

                // 3. Código Fuente
                "git clone -b {$repo->branch} {$repo->url_git} {$path}",

                // 4. Configuración de Entorno (.env)
                "echo \"{$envContent}\" > {$path}/.env",

                // 5. Dependencias y Artisan
                "cd {$path} && composer install --no-dev --optimize-autoloader",
                "cd {$path} && php artisan key:generate",
                "cd {$path} && php artisan jwt:secret --force",
                "cd {$path} && php artisan migrate --seed --force",

                // 5. Configurar NGINX
                "echo '{$nginxConfig}' | sudo tee /etc/nginx/sites-available/{$fullDomain}",
                "sudo ln -sf /etc/nginx/sites-available/{$fullDomain} /etc/nginx/sites-enabled/",

                // 6. SSL con Certbot (Solo si no usas Cloudflare Proxy en modo Flexible)
                "sudo certbot --nginx -d {$fullDomain} --non-interactive --agree-tos -m {$owner->email}",

                "sudo nginx -t && sudo systemctl reload nginx",

                // 6. Permisos Finales
                "sudo chown -R www-data:www-data {$path}",
                "sudo chmod -R 755 {$path}",
                "sudo chmod -R 775 {$path}/storage {$path}/bootstrap/cache",
            ];

            foreach ($commands as $cmd) {
                Log::info("Ejecutando en {$vm->nombre}: {$cmd}");
                $ssh->exec($cmd);
            }

            // MARCAR COMO DESPLEGADO
            $this->dominio->update(['desplegado' => true]);

            Log::info("✅ Despliegue completado para {$fullDomain}");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
