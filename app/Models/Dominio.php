<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dominio extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'subdominio',
        'dominio',
        'dns',
        'path',
        'ip',
        'type',
        'principal',
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
}
