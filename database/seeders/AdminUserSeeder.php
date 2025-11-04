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
        // Check if admin already exists
        if (!User::where('email', 'admin@slpa.lk')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@slpa.lk',
                'password' => Hash::make('Admin@2024'),
                'role' => 'super-admin',
                'is_super_admin' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create a regular admin if needed
        if (!User::where('email', 'clerk@slpa.lk')->exists()) {
            User::create([
                'name' => 'Clerk User',
                'email' => 'clerk@slpa.lk',
                'password' => Hash::make('Clerk@2024'),
                'role' => 'clerk',
                'is_super_admin' => false,
                'email_verified_at' => now(),
            ]);
        }
    }
}
