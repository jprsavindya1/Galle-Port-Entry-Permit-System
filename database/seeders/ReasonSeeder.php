<?php

namespace Database\Seeders;

use App\Models\Reason;
use Illuminate\Database\Seeder;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            ['name' => 'Official Duty'],
            ['name' => 'Meeting'],
            ['name' => 'Inspection'],
            ['name' => 'Maintenance Work'],
            ['name' => 'Repair Work'],
            ['name' => 'Installation Work'],
            ['name' => 'Construction Work'],
            ['name' => 'Cargo Operations'],
            ['name' => 'Container Handling'],
            ['name' => 'Vessel Operations'],
            ['name' => 'Port Operations'],
            ['name' => 'Loading/Unloading'],
            ['name' => 'Equipment Delivery'],
            ['name' => 'Material Delivery'],
            ['name' => 'Fuel Supply'],
            ['name' => 'Water Supply'],
            ['name' => 'Waste Collection'],
            ['name' => 'Security Patrol'],
            ['name' => 'Emergency Response'],
            ['name' => 'Medical Emergency'],
            ['name' => 'Fire Safety'],
            ['name' => 'Training'],
            ['name' => 'Audit'],
            ['name' => 'Customs Clearance'],
            ['name' => 'Immigration Clearance'],
            ['name' => 'Documentation'],
            ['name' => 'Survey'],
            ['name' => 'Technical Support'],
            ['name' => 'IT Support'],
            ['name' => 'Cleaning Services'],
            ['name' => 'Catering Services'],
            ['name' => 'Visitor'],
            ['name' => 'Contractor Work'],
            ['name' => 'Consultancy'],
            ['name' => 'Business Meeting'],
            ['name' => 'Other'],
        ];

        foreach ($reasons as $reason) {
            Reason::create($reason);
        }

        $this->command->info('Reasons seeded successfully!');
    }
}
