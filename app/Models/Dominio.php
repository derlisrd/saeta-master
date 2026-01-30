<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dominio extends Model
{
    protected $fillable = [
        'user_id',
        'zona_id',
        'repositorio_id',
        'vm_id',
        'nombre',
        'subdominio',
        'dominio',
        'dns',
        'path',
        'full_path',
        'ip',
        'type',
        'principal',
        'stack',
        'db_connection',
        'db_pass',
        'db_prefix',
        'db_host',
        'db_user',
        'db_name',
        'db_port',
        'premium',
        'vencimiento',
        'api_key',
        'desplegado',
        'protocol'
    ];

    protected $casts = [
        'desplegado' => 'boolean',
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

    public function vm()
    {
        return $this->belongsTo(VM::class);
    }

    public function repositorio()
    {
        return $this->belongsTo(Repositorio::class, 'repositorio_id', 'id');
    }

    public function envs()
    {
        return $this->hasMany(DominioEnv::class);
    }

    public function db_vms(){
        return $this->belongsTo(DbVms::class,'dominio_id','id');
    }
}
