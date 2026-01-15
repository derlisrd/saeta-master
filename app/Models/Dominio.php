<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dominio extends Model
{
    protected $table = 'dominios';
    protected $fillable = [
        'cliente_id',
        'nombre',
        'subdominio',
        'dominio',
        'dns',
        'type',
        'ip',
        'principal',
        'vencimiento',
        'premium'
    ];
}
