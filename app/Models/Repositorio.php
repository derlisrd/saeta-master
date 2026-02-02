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
        'nombre',
        'url_git',
        'branch',
        'stack_id', // <--- IMPORTANTE
        'tipo_stack',
        'install_commands',
        'build_commands',
        'setup_commands',
        'output_path'
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

    public function stack()
    {
        return $this->belongsTo(Stack::class);
    }

    public function needsBuild(): bool
    {
        return !empty($this->build_commands);
    }

}
