<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default payment settings
        PaymentSetting::create([
            'temporary_permit_rate' => 100.00,
            'monthly_permit_rate' => 2000.00,
            'vehicle_permit_rate' => 500.00,
            'stamp_duty' => 50.00,
            'ssc_rate' => 2.5, // SSC percentage
        ]);

        $this->command->info('Payment settings seeded successfully!');
    }
}
