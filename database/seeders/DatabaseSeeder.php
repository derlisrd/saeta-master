<?php

namespace Database\Seeders;

use App\Models\Dominio;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;  // ✅ Added to generate random strings

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $password = 'decano'; //env('PASSWORD_SEED', 'decano');
        $user = User::factory()->create([
            'name' => 'Derlis',
            'username'=>'derlis',
            'email' => env('EMAIL_SEED','derlisruizdiaz@hotmail.com'),
            'password' => bcrypt( $password )
        ]);
         Dominio::create([
            'user_id'=>$user->id,
            'nombre'=> 'local',
            'protocol'=>'http://',
            'subdominio'=> '192.168.100.31:',
            'dominio'=>'8001',
            'path'=>'api',
            'dns'=>'1',
            'ip'=>'1',
            'type'=>'A',
            'principal'=>true,
            'premium'=>true,
            'vencimiento'=>now()->addDays(15),
            'api_key' => 'WKvn3xFC3JflK8lkIRHVSe60hBFSEjApMZyCnEdwUc' // Str::random(32), 
        ]);
    }
}
