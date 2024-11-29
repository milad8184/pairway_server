<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::create(
            [
                'username' => 'Admin',
                'email' => 'test@test.de',
                'password' => bcrypt('123456'),
                'role' => 'admin',
            ]
        );
    }
}