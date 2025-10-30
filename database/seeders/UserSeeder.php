<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@slpa.lk',
            'password' => Hash::make('password'),
            'role' => 'super-admin',
            'email_verified_at' => now(),
        ]);

        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@slpa.lk',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Clerk/Staff Users
        User::create([
            'name' => 'Clerk One',
            'email' => 'clerk1@slpa.lk',
            'password' => Hash::make('password'),
            'role' => 'clerk',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Clerk Two',
            'email' => 'clerk2@slpa.lk',
            'password' => Hash::make('password'),
            'role' => 'clerk',
            'email_verified_at' => now(),
        ]);

        // Create Staff User
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@slpa.lk',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Users seeded successfully!');
    }
}
