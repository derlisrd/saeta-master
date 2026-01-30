<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stack extends Model
{

    protected $fillable = [
        'slug',
        'nombre',
        'icono',
        'color_hex',
        'descripcion'
    ];


    public function repositorios()
    {
        return $this->hasMany(Repositorio::class);
    }

    public function templates()
    {
        return $this->hasMany(ServerTemplate::class);
    }
}
