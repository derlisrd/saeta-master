<?php

namespace Database\Seeders;

use App\Models\Dominio;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $password = bcrypt(env('PASSWORD_SEED', 'decano'));
        $user = User::factory()->create([
            'name' => 'Derlis',
            'email' => env('EMAIL_SEED','derlisruizdiaz@hotmail.com'),
            'password' => $password
        ]);
        $dominio = Dominio::create([
            'cliente_id'=>$user->id,
            'nombre'=> 'local',
            'protocol'=>'http://',
            'subdominio'=> '192.168.100.31:',
            'dominio'=>'8001',
            'dns'=>'1',
            'ip'=>'1',
            'type'=>'A',
            'principal'=>true,
            'premium'=>true,
            'vencimiento'=>now()->addDays(15),
        ]);
    }
}
