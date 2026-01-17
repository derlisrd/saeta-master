<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dominio extends Model
{
    protected $fillable = [
        'cliente_id',
        'nombre',
        'subdominio',
        'dominio',
        'dns',
        'ip',
        'type',
        'principal',
        'premium',
        'vencimiento',
    ];

    protected $casts = [
        'principal' => 'boolean',
        'premium' => 'boolean',
        'vencimiento' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
