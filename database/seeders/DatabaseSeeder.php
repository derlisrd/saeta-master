<?php

namespace Database\Seeders;

use App\Models\Admin;

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
        // User::factory(10)->create();
    }
}
