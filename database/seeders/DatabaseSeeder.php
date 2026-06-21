<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User if they do not already exist
        User::firstOrCreate(
            ['email' => 'lintasan2098@gmail.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now(),
            ]
        );

        $this->command->info('Database seeded successfully!');
        $this->command->info('✅ Created: 1 admin user');
        $this->command->info('');
        $this->command->info('🔑 Login Credentials:');
        $this->command->info('Email: lintasan2098@gmail.com');
        $this->command->info('Password: password');
    }
}
