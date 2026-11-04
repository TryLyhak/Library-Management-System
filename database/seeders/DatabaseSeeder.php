<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::updateOrCreate(
            ['email' => 'scott@gmail.com'],   // how to find the user
            [
                'name' => 'Scott Admin',
                'password' => '123',        // hashed automatically (see below)
            ]
        );
    }
}
