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
        // Create default payment settings only if table is empty
        if (PaymentSetting::count() === 0) {
            PaymentSetting::create([
                'rate' => 100.00,           // Base rate per day
                'ssl' => 2.5,               // SSL percentage (2.5%)
                'vat' => 18.00,             // VAT percentage (18%)
                'price_onboard' => 100.00,  // Onboard pass price
                'price_afloat' => 80.00,    // Afloat pass price
                'price_ashore' => 50.00,    // Ashore pass price
            ]);

            $this->command->info('Payment settings seeded successfully!');
        } else {
            $this->command->info('Payment settings already exist, skipping seed.');
        }
    }
}
