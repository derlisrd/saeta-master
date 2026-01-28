<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DbVms extends Model
{
    protected $table = 'db_vms';
    protected $fillable = [
        'dominio_id',
        'host',
        'port',
        'db_port',
        'db_name',
        'db_pass',
        'db_connection'
    ];
}
