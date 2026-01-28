server {
    listen 80;
    server_name {{ $domain }};
    root {{ $basePath }};

    # Configuración para /admin - Aplicación React (Vite/Dist)
    location /admin {
        alias {{ $basePath }}/admin/dist/;
        index index.html;
        # Escapamos $uri para que Blade no lo procese
        try_files $uri $uri/ /admin/index.html;
    }

    # Servir archivos estáticos de React directamente
    location ~ ^/admin/(.*\.(js|css|png|jpg|jpeg|gif|svg|ico|json|woff|woff2|ttf|eot))$ {
        alias {{ $basePath }}/admin/dist/$1;
        access_log off;
        expires max;
    }

    # Configuración para /v1 - Laravel API
    location /v1 {
        # Escapamos $uri y $query_string
        try_files $uri $uri/ /v1/index.php?$query_string;
    }

    # Procesar index.php para la API en /v1
    location ~ ^/v1/index\.php(/|$) {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        # Aquí usamos las variables de Blade correctamente
        fastcgi_param SCRIPT_FILENAME {{ $path }}/public/index.php;
        fastcgi_param DOCUMENT_ROOT {{ $path }}/public;
    }

    # Configuración por defecto para la raíz
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Manejo de archivos PHP generales
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Denegar acceso a archivos sensibles
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Logs - Usamos llaves para evitar ambigüedad en el nombre del archivo
    error_log /var/log/nginx/{{ $domain }}.error.log;
    access_log /var/log/nginx/{{ $domain }}.access.log;
}