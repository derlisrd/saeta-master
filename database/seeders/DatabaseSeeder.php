<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Stack;
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
        $password = env('PASSWORD_SEED');
        $password = Hash::make($password); 

        Admin::create([
            'name' => 'Administrador',
            'email' => env('EMAIL_SEED'),
            'password' =>$password
        ]);
        User::create([
            'name' => 'Administrador',
            'username' => env('EMAIL_SEED'),
            'email' => env('EMAIL_SEED'),
            'password' =>$password
        ]);

        Stack::create([
            'nombre'=>'Laravel',
            'slug'=>'laravel',
            'color_hex'=> '#FF2D20',
            'icon'=> 'fa-laravel'
        ]);
        // User::factory(10)->create();
    }
}
