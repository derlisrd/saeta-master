<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dominio extends Model
{
    protected $fillable = [
        'user_id',
        'zona_id',
        'nombre',
        'subdominio',
        'dominio',
        'dns',
        'path',
        'ip',
        'type',
        'principal',
        'bd_pass',
        'bd_prefix',
        'bd_user',
        'bd_name',
        'premium',
        'vencimiento',
        'api_key'
    ];

    protected $casts = [
        'principal' => 'boolean',
        'premium' => 'boolean',
        'vencimiento' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function zona()
    {
        // Relacionamos zone_id de Dominios con zone_id de Zones
        return $this->belongsTo(Zone::class, 'zone_id', 'zone_id');
    }
}
