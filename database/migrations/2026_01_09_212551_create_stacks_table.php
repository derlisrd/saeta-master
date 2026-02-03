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
        Schema::create('stacks', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: React, Node.js, Laravel, Vue, Python
            $table->string('slug')->unique(); // Ej: react, node-js, laravel
            $table->string('icon')->nullable(); // Clase de FontAwesome o URL de SVG
            $table->string('color_hex')->default('#3b82f6'); // Para personalizar la UI según el stack
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stacks');
    }
};
