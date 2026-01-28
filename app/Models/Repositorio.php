<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Repositorio extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención)
    protected $table = 'repositorios';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',      // Nombre descriptivo (ej: Core Backend)
        'url_git',     // URL completa del repo (https://...)
        'branch',      // Rama por defecto (main/master)
        'tipo',        // laravel, nodejs, static, wordpress
        'descripcion'  // Notas adicionales
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // Si usas PHP 8.1+ Enums, aquí podrías poner: 
        // 'tipo' => RepoTipo::class 
    ];

    /**
     * Relación con los Dominios.
     * Un repositorio puede estar desplegado en muchos dominios/instancias.
     */
    public function dominios()
    {
        return $this->hasMany(Dominio::class, 'repositorio_id');
    }
}
