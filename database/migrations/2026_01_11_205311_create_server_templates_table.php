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
        Schema::create('server_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Laravel Premium, React SPA, Static HTML
            $table->string('web_server')->default('nginx'); //['nginx', 'apache']
            $table->text('config_content'); // Aquí va el código con {{dominio}} y {{path}}
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_templates');
    }
};
