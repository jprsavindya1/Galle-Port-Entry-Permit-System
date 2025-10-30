<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            ['name' => 'Sri Lanka Ports Authority'],
            ['name' => 'Ceylon Shipping Corporation'],
            ['name' => 'Colombo International Container Terminals Limited (CICT)'],
            ['name' => 'South Asia Gateway Terminals (SAGT)'],
            ['name' => 'Jaya Container Terminals (JCT)'],
            ['name' => 'Sri Lanka Customs'],
            ['name' => 'Ceylon Petroleum Corporation (CPC)'],
            ['name' => 'Ceylon Electricity Board (CEB)'],
            ['name' => 'National Water Supply and Drainage Board'],
            ['name' => 'Sri Lanka Navy'],
            ['name' => 'Sri Lanka Police'],
            ['name' => 'Immigration and Emigration Department'],
            ['name' => 'Department of Commerce'],
            ['name' => 'Export Development Board'],
            ['name' => 'John Keells Holdings'],
            ['name' => 'Hayleys Group'],
            ['name' => 'Maersk Line'],
            ['name' => 'MSC Mediterranean Shipping Company'],
            ['name' => 'CMA CGM'],
            ['name' => 'Hapag-Lloyd'],
            ['name' => 'OOCL (Orient Overseas Container Line)'],
            ['name' => 'Evergreen Marine Corporation'],
            ['name' => 'Freight Forwarders Association'],
            ['name' => 'Chamber of Commerce'],
            ['name' => 'Port Security Division'],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }

        $this->command->info('Companies seeded successfully!');
    }
}
