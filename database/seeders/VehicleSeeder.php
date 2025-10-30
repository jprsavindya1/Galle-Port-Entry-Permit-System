<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            ['name' => 'Car', 'code' => 'CAR', 'rate' => 500.00],
            ['name' => 'Van', 'code' => 'VAN', 'rate' => 750.00],
            ['name' => 'Bus', 'code' => 'BUS', 'rate' => 1000.00],
            ['name' => 'Mini Bus', 'code' => 'MB', 'rate' => 800.00],
            ['name' => 'Lorry (Small)', 'code' => 'LS', 'rate' => 1000.00],
            ['name' => 'Lorry (Medium)', 'code' => 'LM', 'rate' => 1500.00],
            ['name' => 'Lorry (Large)', 'code' => 'LL', 'rate' => 2000.00],
            ['name' => 'Truck', 'code' => 'TRK', 'rate' => 2500.00],
            ['name' => 'Prime Mover', 'code' => 'PM', 'rate' => 3000.00],
            ['name' => 'Trailer', 'code' => 'TRL', 'rate' => 3500.00],
            ['name' => 'Container Trailer', 'code' => 'CT', 'rate' => 4000.00],
            ['name' => 'Three Wheeler', 'code' => 'TW', 'rate' => 300.00],
            ['name' => 'Motorcycle', 'code' => 'MC', 'rate' => 200.00],
            ['name' => 'Crane', 'code' => 'CRN', 'rate' => 5000.00],
            ['name' => 'Forklift', 'code' => 'FL', 'rate' => 2000.00],
            ['name' => 'Heavy Equipment', 'code' => 'HE', 'rate' => 4000.00],
            ['name' => 'Tanker', 'code' => 'TNK', 'rate' => 3500.00],
            ['name' => 'Ambulance', 'code' => 'AMB', 'rate' => 500.00],
            ['name' => 'Fire Truck', 'code' => 'FT', 'rate' => 1000.00],
            ['name' => 'Towing Vehicle', 'code' => 'TOW', 'rate' => 1500.00],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }

        $this->command->info('Vehicles seeded successfully!');
    }
}
