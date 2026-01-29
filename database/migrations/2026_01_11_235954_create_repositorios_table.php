<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repositorios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: "Laravel Boilerplate" o "Frontend React"
            $table->string('url_git'); // Ej: https://github.com/tu-usuario/repo.git
            $table->string('branch')->default('main');
            $table->string('tipo')->default('laravel'); // En lugar de enum
            //$table->enum('tipo', ['laravel', 'nodejs', 'static', 'wordpress'])->default('laravel');
            $table->text('descripcion')->nullable();
            $table->text('install_commands')->nullable(); // Ej: composer install, npm install
            $table->text('build_commands')->nullable();   // Ej: npm run build, vite build
            $table->text('setup_commands')->nullable();   // Ej: php artisan migrate, php artisan key:generate
            $table->string('output_path')->default('public'); // Para Frontends: carpeta donde queda el build (dist, build, public)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositorios');
    }
};
