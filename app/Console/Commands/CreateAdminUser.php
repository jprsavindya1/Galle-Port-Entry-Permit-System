<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'user:create-admin {email?} {password?}';
    protected $description = 'Create an admin user';

    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@slpa.lk';
        $password = $this->argument('password') ?? 'Admin123';

        if (User::where('email', $email)->exists()) {
            $this->error('User with this email already exists!');
            return 1;
        }

        $user = User::create([
            'name' => 'Super Admin',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'super-admin',
            'is_super_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->info('Admin user created successfully!');
        $this->info('Email: ' . $email);
        $this->info('Password: ' . $password);
        
        return 0;
    }
}
