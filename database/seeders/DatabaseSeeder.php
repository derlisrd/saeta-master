<?php

namespace Database\Seeders;

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
        $password = bcrypt(env('PASSWORD_SEED', '110309393'));
        User::factory()->create([
            'name' => 'Derlis',
            'email' => env('EMAIL_SEED','derlis@gmail.com'),
            'password' => $password
        ]);
    }
}
