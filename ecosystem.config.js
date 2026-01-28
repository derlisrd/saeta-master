export const apps = [
    {
        name: "maestro-queue-worker",
        script: "artisan",
        args: "queue:work --sleep=3 --tries=3 --max-time=3600",
        interpreter: "php",
        instances: 1,
        exec_mode: "fork",
        watch: false,
        autorestart: true,
        max_memory_restart: "200M",
        error_file: "./storage/logs/pm2-queue-error.log",
        out_file: "./storage/logs/pm2-queue-out.log",
    },
];

// Opcional: export default { apps };
