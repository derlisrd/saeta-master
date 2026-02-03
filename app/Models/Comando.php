<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comando extends Model
{
    protected $table = 'comandos';
    protected $fillable = [
        'repositorio_id',
        'orden',
        'comando',
        'descripcion',
        'ignore_error',
    ];
}
