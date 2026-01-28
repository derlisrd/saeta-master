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
        Schema::create('dominios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('zone_id')->unsigned()->nullable();
            $table->string('nombre')->nullable();
            $table->string('protocol')->nullable();
            $table->string('subdominio')->unique()->nullable();
            $table->string('dominio');
            $table->string('dns')->nullable();
            $table->string('path')->nullable();
            $table->string('ip')->nullable();
            $table->text('api_key')->nullable();
            $table->string('type')->default('A')->nullable();
            $table->boolean('principal')->default(0);
            $table->boolean('premium')->default(0);
            $table->string('db_prefix')->nullable();
            $table->string('db_name')->nullable();
            $table->string('db_user')->nullable();
            $table->string('db_pass')->nullable();
            $table->boolean('desplegado')->default(0);
            $table->date('vencimiento');
            // En la migración de dominios
            $table->foreignId('repositorio_id')->nullable()->constrained('repositorios');
            $table->foreign('user_id')->on('users')->references('id');
            $table->foreign('zone_id')->on('zones')->references('id');
            $table->foreignId('vm_id')->nullable()->constrained('vms')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dominios');
    }
};
