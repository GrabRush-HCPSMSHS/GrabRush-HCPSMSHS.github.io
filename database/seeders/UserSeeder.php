<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'role' => UserRole::Staff,
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'role' => UserRole::Customer,
            'email_verified_at' => now(),
        ]);
    }
}
