<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\ServerTemplate;
use App\Models\Stack;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Str;  // ✅ Added to generate random strings

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = env('PASSWORD_SEED');
        $password = Hash::make($password);

        Admin::create([
            'name' => 'Administrador',
            'email' => env('EMAIL_SEED'),
            'password' => $password
        ]);
        User::create([
            'name' => 'Administrador',
            'username' => env('EMAIL_SEED'),
            'email' => env('EMAIL_SEED'),
            'password' => $password
        ]);

        $stackLaravel = Stack::create([
            'nombre' => 'Laravel',
            'slug' => 'laravel',
            'color_hex' => '#FF2D20',
            'icon' => 'fa-laravel'
        ]);
        $configNginxLaravel = <<<'EOD'
server {
    listen 80;
    server_name {{dominio}};
    root {{output_path}};

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php{{php_version}}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOD;
        ServerTemplate::create([
            'nombre' => 'Laravel Nginx Standard',
            'stack_id' => $stackLaravel->id, // <--- VINCULACIÓN CLAVE
            'web_server' => 'nginx',
            'descripcion' => 'Plantilla optimizada para proyectos Laravel con Nginx',
            'config_content' => $configNginxLaravel
        ]);
    }
}
