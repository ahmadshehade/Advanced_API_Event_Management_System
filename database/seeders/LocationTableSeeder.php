<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('locations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $locations = [
            [
                'name' => 'Cairo International Conference Center',
                'address' => 'Nasr City, Cairo, Egypt',
            ],
            [
                'name' => 'King Abdulaziz Center for World Culture',
                'address' => 'Dhahran, Saudi Arabia',
            ],
            [
                'name' => 'Dubai World Trade Centre',
                'address' => 'Sheikh Zayed Road, Dubai, UAE',
            ],
            [
                'name' => 'Doha Exhibition and Convention Center',
                'address' => 'West Bay, Doha, Qatar',
            ],
            [
                'name' => 'Bibliotheca Alexandrina',
                'address' => 'Corniche El Geish Rd, Alexandria, Egypt',
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    
    }
}
