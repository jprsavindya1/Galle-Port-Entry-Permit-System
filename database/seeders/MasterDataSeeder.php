<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Designation;
use App\Models\Reason;
use App\Models\Vehicle;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Companies
        $companies = [
            ['name' => 'ABC Shipping Ltd', 'address' => 'Colombo Port, Sri Lanka'],
            ['name' => 'XYZ Logistics', 'address' => 'Galle Road, Colombo 03'],
            ['name' => 'Maritime Services Inc', 'address' => 'Port City, Colombo'],
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(['name' => $company['name']], $company);
        }

        // Seed Designations
        $designations = [
            ['name' => 'Manager'],
            ['name' => 'Supervisor'],
            ['name' => 'Officer'],
            ['name' => 'Driver'],
            ['name' => 'Operator'],
        ];

        foreach ($designations as $designation) {
            Designation::firstOrCreate($designation);
        }

        // Seed Reasons
        $reasons = [
            ['name' => 'Official Duty'],
            ['name' => 'Cargo Inspection'],
            ['name' => 'Maintenance Work'],
            ['name' => 'Emergency Response'],
            ['name' => 'Delivery'],
        ];

        foreach ($reasons as $reason) {
            Reason::firstOrCreate($reason);
        }

        // Seed Vehicles
        $vehicles = [
            ['name' => 'Car', 'code' => 'CAR', 'rate' => 500.00],
            ['name' => 'Van', 'code' => 'VAN', 'rate' => 750.00],
            ['name' => 'Truck', 'code' => 'TRUCK', 'rate' => 1000.00],
            ['name' => 'Bus', 'code' => 'BUS', 'rate' => 1200.00],
            ['name' => 'Motorcycle', 'code' => 'BIKE', 'rate' => 300.00],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::firstOrCreate(['code' => $vehicle['code']], $vehicle);
        }

        $this->command->info('Master data seeded successfully!');
    }
}
