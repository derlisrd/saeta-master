<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2;

class VPSService
{
    /**
     * Configurar dominio en el VPS
     */
    public function setupDomain($config)
    {
        $ssh = new SSH2($config['vps_host']);

        if (!$ssh->login($config['vps_user'], $config['vps_password'])) {
            throw new \Exception('Fallo la autenticación SSH');
        }

        $subdominio = $config['subdominio'];
        $fullDomain = $config['full_domain'];
        $baseDir = "/var/www/html/{$subdominio}";

        // 1. Crear estructura de directorios
        $this->createDirectories($ssh, $baseDir);

        // 2. Clonar repositorios
        $this->cloneRepositories($ssh, $baseDir, $config['repo_core'], $config['repo_admin']);

        // 3. Configurar Laravel (core)
        $this->setupLaravel($ssh, $baseDir, $subdominio);

        // 4. Configurar React (admin)
        $this->setupReact($ssh, $baseDir);

        // 5. Crear configuración Nginx
        $this->createNginxConfig($ssh, $subdominio, $fullDomain, $baseDir);

        // 6. Recargar Nginx
        $ssh->exec('sudo systemctl reload nginx');

        Log::info("VPS configurado para {$fullDomain}");
    }

    protected function createDirectories($ssh, $baseDir)
    {
        $commands = [
            "sudo mkdir -p {$baseDir}/core",
            "sudo mkdir -p {$baseDir}/admin",
            "sudo chown -R www-data:www-data {$baseDir}",
            "sudo chmod -R 755 {$baseDir}",
        ];

        foreach ($commands as $command) {
            $ssh->exec($command);
        }
    }

    protected function cloneRepositories($ssh, $baseDir, $repoCore, $repoAdmin)
    {
        // Clonar Laravel
        $ssh->exec("cd {$baseDir}/core && git clone {$repoCore} .");

        // Clonar React
        $ssh->exec("cd {$baseDir}/admin && git clone {$repoAdmin} .");
    }

    protected function setupLaravel($ssh, $baseDir, $subdominio)
    {
        $coreDir = "{$baseDir}/core";
        $dbName = "db_{$subdominio}";
        $dbUser = "user_{$subdominio}";
        $dbPassword = $this->generatePassword();

        // Composer install
        $ssh->exec("cd {$coreDir} && composer install --no-dev --optimize-autoloader");

        // Crear .env
        $envContent = $this->generateLaravelEnv($dbName, $dbUser, $dbPassword);
        $ssh->exec("echo '{$envContent}' > {$coreDir}/.env");

        // Generar key
        $ssh->exec("cd {$coreDir} && php artisan key:generate");

        // Crear base de datos y usuario
        $this->createDatabase($ssh, $dbName, $dbUser, $dbPassword);

        // Migraciones
        $ssh->exec("cd {$coreDir} && php artisan migrate --force");

        // Permisos
        $ssh->exec("sudo chown -R www-data:www-data {$coreDir}/storage {$coreDir}/bootstrap/cache");
        $ssh->exec("sudo chmod -R 775 {$coreDir}/storage {$coreDir}/bootstrap/cache");
    }

    protected function setupReact($ssh, $baseDir)
    {
        $adminDir = "{$baseDir}/admin";

        // Crear .env para React (ajusta según tus necesidades)
        $envContent = "VITE_API_URL=/v1\n";
        $ssh->exec("echo '{$envContent}' > {$adminDir}/.env");

        // npm install y build
        $ssh->exec("cd {$adminDir} && npm install");
        $ssh->exec("cd {$adminDir} && npm run build");
    }

    protected function createDatabase($ssh, $dbName, $dbUser, $dbPassword)
    {
        $mysqlRootPassword = config('services.mysql.root_password');

        $commands = [
            "sudo mysql -e \"CREATE DATABASE IF NOT EXISTS {$dbName};\"",
            "sudo mysql -e \"CREATE USER IF NOT EXISTS '{$dbUser}'@'localhost' IDENTIFIED BY '{$dbPassword}';\"",
            "sudo mysql -e \"GRANT ALL PRIVILEGES ON {$dbName}.* TO '{$dbUser}'@'localhost';\"",
            "sudo mysql -e \"FLUSH PRIVILEGES;\"",
        ];

        foreach ($commands as $command) {
            $ssh->exec($command);
        }
    }

    protected function createNginxConfig($ssh, $subdominio, $fullDomain, $baseDir)
    {
        $nginxConfig = <<<NGINX
server {
    listen 80;
    server_name {$fullDomain};
    root {$baseDir};

    # Configuración para /admin - Aplicación React
    location /admin {
        alias {$baseDir}/admin/dist/;
        index index.html;
        try_files \$uri \$uri/ /admin/index.html;
    }

    # Servir los archivos estáticos de React directamente
    location ~ ^/admin/(.*\.(js|css|png|jpg|jpeg|gif|svg|ico|json|woff|woff2|ttf|eot))\$ {
        alias {$baseDir}/admin/dist/\$1;
    }

    # Configuración para /v1 - API Laravel
    location /v1 {
        try_files \$uri \$uri/ /v1/index.php?\$query_string;
    }

    # Procesar index.php para las rutas /v1
    location ~ ^/v1/index\.php(/|$) {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME {$baseDir}/core/public/index.php;
        include fastcgi_params;
    }

    # Denegar acceso a archivos .ht
    location ~ /\.ht {
        deny all;
    }
}
NGINX;

        $configPath = "/etc/nginx/sites-available/{$subdominio}";
        $ssh->exec("echo '{$nginxConfig}' | sudo tee {$configPath}");
        $ssh->exec("sudo ln -sf {$configPath} /etc/nginx/sites-enabled/{$subdominio}");
        $ssh->exec("sudo nginx -t");
    }

    protected function generateLaravelEnv($dbName, $dbUser, $dbPassword)
    {
        return <<<ENV
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={$dbName}
DB_USERNAME={$dbUser}
DB_PASSWORD={$dbPassword}
ENV;
    }

    protected function generatePassword($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }
}
