module.exports = {
    apps: [
        {
            name: "maestro-queue-worker", // Nombre del proceso en PM2
            script: "artisan",
            args: "queue:work --sleep=3 --tries=3 --max-time=3600",
            interpreter: "php",
            instances: 1, // Puedes subirlo a 2 o más si tienes muchos jobs
            exec_mode: "fork", // Laravel artisan requiere modo fork
            watch: false, // No reiniciar al cambiar archivos (evita loops en deploy)
            autorestart: true, // Reiniciar si el proceso falla
            max_memory_restart: "200M", // Si el worker gasta mucha RAM, PM2 lo reinicia limpio
            error_file: "./storage/logs/pm2-queue-error.log",
            out_file: "./storage/logs/pm2-queue-out.log",
            env: {
                NODE_ENV: "production",
            },
        },
    ],
};
