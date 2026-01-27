<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Dominio;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Str;  // ✅ Added to generate random strings

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('decano10');
        Admin::create([
            'name' => 'Derlis',
            'email' => 'derlisruizdiaz@hotmail.com',
            'password' =>$password
        ]);
        // User::factory(10)->create();
        $password = Hash::make('decano10'); //env('PASSWORD_SEED', 'decano');
       /*  $user = User::factory()->create([
            'name' => 'Derlis',
            'username'=>'derlis',
            'email' => env('EMAIL_SEED','derlisruizdiaz@hotmail.com'),
            'password' =>  $password 
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
        ]); */
    }
}
