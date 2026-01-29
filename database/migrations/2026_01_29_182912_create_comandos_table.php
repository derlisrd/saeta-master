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
        Schema::create('comandos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('repositorio_id')->unsigned()->nullable();
            $table->string('orden')->default(0); // Para saber qué comando va primero
            $table->string('comando');           // El comando real: ej. "npm install"
            $table->string('descripcion')->nullable();
            $table->boolean('ignore_error')->default(false); // Por si un comando puede fallar sin detener todo
            $table->foreign('repositorio_id')->on('repositorios')->references('id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comandos');
    }
};
