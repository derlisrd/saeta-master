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
