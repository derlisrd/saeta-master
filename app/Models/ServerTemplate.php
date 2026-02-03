<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerTemplate extends Model
{
    protected $table = 'server_templates';

    protected $fillable = [
        'server_templates',
        'stack_id',
        'nombre',
        'web_server',
        'config_context',
        'descripcion'
    ];
}
