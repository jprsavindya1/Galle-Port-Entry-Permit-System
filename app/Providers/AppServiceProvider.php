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
        // Force HTTPS in production
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
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
                
                // Auto-seed master data tables if empty
                if (\App\Models\Company::count() === 0 || \App\Models\Vehicle::count() === 0) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', [
                        '--class' => 'Database\\Seeders\\MasterDataSeeder',
                        '--force' => true
                    ]);
                    \Illuminate\Support\Facades\Log::info('Auto-seeded master data tables');
                }
            } catch (\Exception $e) {
                // Silently fail if database not ready yet
                \Illuminate\Support\Facades\Log::warning('Could not auto-seed data: ' . $e->getMessage());
            }
        }
    }
}
