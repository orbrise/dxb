<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GeoDomain;

class GeoDomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domains = [
            [
                'country_code' => 'AE',
                'country_name' => 'United Arab Emirates',
                'domain' => 'ae.massagerepublic.com.co',
                'is_active' => true,
                'priority' => 10,
            ],
            [
                'country_code' => 'PK',
                'country_name' => 'Pakistan',
                'domain' => 'pk.massagerepublic.com.co',
                'is_active' => true,
                'priority' => 10,
            ],
            [
                'country_code' => 'DE',
                'country_name' => 'Germany',
                'domain' => 'de.massagerepublic.com.co',
                'is_active' => false,
                'priority' => 5,
            ],
            [
                'country_code' => 'GB',
                'country_name' => 'United Kingdom',
                'domain' => 'gb.massagerepublic.com.co',
                'is_active' => false,
                'priority' => 5,
            ],
            [
                'country_code' => 'US',
                'country_name' => 'United States',
                'domain' => 'us.massagerepublic.com.co',
                'is_active' => false,
                'priority' => 5,
            ],
            [
                'country_code' => 'IN',
                'country_name' => 'India',
                'domain' => 'in.massagerepublic.com.co',
                'is_active' => false,
                'priority' => 5,
            ],
            [
                'country_code' => 'SA',
                'country_name' => 'Saudi Arabia',
                'domain' => 'sa.massagerepublic.com.co',
                'is_active' => false,
                'priority' => 8,
            ],
            [
                'country_code' => 'QA',
                'country_name' => 'Qatar',
                'domain' => 'qa.massagerepublic.com.co',
                'is_active' => false,
                'priority' => 8,
            ],
            [
                'country_code' => 'KW',
                'country_name' => 'Kuwait',
                'domain' => 'kw.massagerepublic.com.co',
                'is_active' => false,
                'priority' => 8,
            ],
            [
                'country_code' => 'BH',
                'country_name' => 'Bahrain',
                'domain' => 'bh.massagerepublic.com.co',
                'is_active' => false,
                'priority' => 8,
            ],
            [
                'country_code' => 'OM',
                'country_name' => 'Oman',
                'domain' => 'om.massagerepublic.com.co',
                'is_active' => false,
                'priority' => 8,
            ],
        ];

        foreach ($domains as $domain) {
            GeoDomain::updateOrCreate(
                ['country_code' => $domain['country_code']],
                $domain
            );
        }

        $this->command->info('Geo domains seeded successfully!');
    }
}
