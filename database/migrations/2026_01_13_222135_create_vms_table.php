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
        Schema::create('vms', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: DigitalOcean Droplet 1
            $table->string('ip')->unique();
            $table->string('usuario')->default('root');
            $table->text('ssh_key')->nullable(); // Por si usas llaves distintas por VM
            $table->integer('puerto')->default(22);
            $table->string('so')->nullable(); // Ej: Ubuntu 22.04
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vms');
    }
};
