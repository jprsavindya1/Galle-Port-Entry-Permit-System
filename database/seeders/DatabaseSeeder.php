<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * This will populate the database with:
     * - Default users (Super Admin, Admin, Clerks, Staff)
     * - Companies master data
     * - Designations master data
     * - Vehicle types with rates
     * - Entry reasons
     * - Payment settings configuration
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();

        // Seed users first
        $this->command->info('👤 Seeding users...');
        $this->call(UserSeeder::class);
        $this->command->newLine();

        // Seed payment settings
        $this->command->info('💰 Seeding payment settings...');
        $this->call(PaymentSettingSeeder::class);
        $this->command->newLine();

        // Seed master data
        $this->command->info('🏢 Seeding companies...');
        $this->call(CompanySeeder::class);
        $this->command->newLine();

        $this->command->info('👔 Seeding designations...');
        $this->call(DesignationSeeder::class);
        $this->command->newLine();

        $this->command->info('🚗 Seeding vehicles...');
        $this->call(VehicleSeeder::class);
        $this->command->newLine();

        $this->command->info('📋 Seeding entry reasons...');
        $this->call(ReasonSeeder::class);
        $this->command->newLine();

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        
        // Display login credentials
        $this->command->warn('=================================================');
        $this->command->warn('            DEFAULT LOGIN CREDENTIALS            ');
        $this->command->warn('=================================================');
        $this->command->info('Super Admin:');
        $this->command->line('  Email: superadmin@slpa.lk');
        $this->command->line('  Password: password');
        $this->command->newLine();
        $this->command->info('Admin:');
        $this->command->line('  Email: admin@slpa.lk');
        $this->command->line('  Password: password');
        $this->command->newLine();
        $this->command->info('Clerk 1:');
        $this->command->line('  Email: clerk1@slpa.lk');
        $this->command->line('  Password: password');
        $this->command->newLine();
        $this->command->info('Clerk 2:');
        $this->command->line('  Email: clerk2@slpa.lk');
        $this->command->line('  Password: password');
        $this->command->newLine();
        $this->command->info('Staff:');
        $this->command->line('  Email: staff@slpa.lk');
        $this->command->line('  Password: password');
        $this->command->warn('=================================================');
        $this->command->warn('⚠️  CHANGE THESE PASSWORDS IN PRODUCTION!  ⚠️');
        $this->command->warn('=================================================');
    }
}
