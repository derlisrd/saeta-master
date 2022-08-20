<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $pass = env('PERSONAL_PASSWORD');
        $data = [
            'name'=>'Derlis Ruiz Diaz',
            'email'=>'derlisruizdiazr@gmail.com',
            'username'=>'derlis',
            'password'=>Hash::make($pass)
        ];

        User::create($data);

        // User::factory(10)->create();
    }
}
