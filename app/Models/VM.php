<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VM extends Model
{
    protected $table = 'vms';


    protected $fillable = [
        'nombre',
        'ip',
        'usuario',
        'ssh_key',
        'puerto',
        'so',
        'web_server_type'
    ];

    protected $casts = [
        'ssh_key' => 'encrypted', // Laravel cifra y descifra automáticamente
    ];

    public function dominios()
    {
        return $this->hasMany(Dominio::class,'vm_id');
    }
}
