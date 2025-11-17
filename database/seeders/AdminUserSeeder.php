<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@keobongda.co'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'), // Change this password!
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created!');
        $this->command->info('Email: admin@keobongda.co');
        $this->command->info('Password: admin123');
        $this->command->warn('⚠️  Please change the password after first login!');
    }
}
