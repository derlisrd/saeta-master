<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    protected $table = 'negocios';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'activo',
        'user_id',
        'temporal' // Solo para marcarlo como temporal, no se guarda así
    ];

     /**
     * Obtener el usuario asociado al negocio.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
