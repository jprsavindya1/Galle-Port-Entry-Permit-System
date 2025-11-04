<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-create admin user if none exists (production safety)
        if (app()->environment('production')) {
            try {
                if (\App\Models\User::count() === 0) {
                    \App\Models\User::create([
                        'name' => 'Super Admin',
                        'email' => 'admin@slpa.lk',
                        'password' => \Illuminate\Support\Facades\Hash::make('Admin123'),
                        'role' => 'super-admin',
                        'is_super_admin' => true,
                        'email_verified_at' => now(),
                    ]);
                    \Illuminate\Support\Facades\Log::info('Auto-created admin user: admin@slpa.lk');
                }
            } catch (\Exception $e) {
                // Silently fail if database not ready yet
                \Illuminate\Support\Facades\Log::warning('Could not auto-create admin: ' . $e->getMessage());
            }
        }
    }
}
