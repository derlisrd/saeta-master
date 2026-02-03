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
            $table->string('nombre'); // Ej: "E-commerce API"
            $table->string('url_git');
            $table->string('branch')->default('main');

            // RELACIONES CLAVE
            // Vinculamos al Stack (Laravel, React, etc.)
            $table->foreignId('stack_id')->constrained('stacks')->onDelete('cascade');

            $table->text('descripcion')->nullable();

            $table->string('tipo_stack')->nullable();

            // PIPELINE DE DESPLIEGUE (Comandos multilínea)
            $table->text('install_commands')->nullable();
            $table->text('build_commands')->nullable();
            $table->text('setup_commands')->nullable();

            // CONFIGURACIÓN DE SALIDA
            // Donde vive el index.php o index.html final
            $table->string('output_path')->default('public');

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
