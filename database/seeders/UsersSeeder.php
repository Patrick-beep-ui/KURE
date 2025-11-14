<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        User::factory()->count(5)->create();

        // Ensure at least one admin
        User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com'
        ]);
    }
}
