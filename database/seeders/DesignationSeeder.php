<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            ['name' => 'General Manager'],
            ['name' => 'Deputy General Manager'],
            ['name' => 'Assistant General Manager'],
            ['name' => 'Port Director'],
            ['name' => 'Operations Manager'],
            ['name' => 'Terminal Manager'],
            ['name' => 'Engineering Manager'],
            ['name' => 'Safety Officer'],
            ['name' => 'Security Officer'],
            ['name' => 'Customs Officer'],
            ['name' => 'Immigration Officer'],
            ['name' => 'Harbour Master'],
            ['name' => 'Pilot'],
            ['name' => 'Tug Master'],
            ['name' => 'Crane Operator'],
            ['name' => 'Forklift Operator'],
            ['name' => 'Technician'],
            ['name' => 'Electrician'],
            ['name' => 'Mechanic'],
            ['name' => 'Engineer'],
            ['name' => 'Supervisor'],
            ['name' => 'Inspector'],
            ['name' => 'Clerk'],
            ['name' => 'Accountant'],
            ['name' => 'Admin Officer'],
            ['name' => 'IT Officer'],
            ['name' => 'Driver'],
            ['name' => 'Labourer'],
            ['name' => 'Contractor'],
            ['name' => 'Consultant'],
            ['name' => 'Visitor'],
            ['name' => 'Vendor'],
            ['name' => 'Service Provider'],
            ['name' => 'Delivery Personnel'],
        ];

        foreach ($designations as $designation) {
            Designation::create($designation);
        }

        $this->command->info('Designations seeded successfully!');
    }
}
