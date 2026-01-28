<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DominioEnv extends Model
{
    protected $table = 'dominio_envs';
    protected $fillable = [
        'key',
        'value',
        'dominio_id'
    ];
}
