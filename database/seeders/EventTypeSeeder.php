<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
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

        $types = [
            'Conference',
            'Workshop',
            'Seminar',
            'Webinar',
            'Concert',
            'Festival',
            'Networking Event',
            'Fundraiser',
            'Product Launch',
            'Exhibition'
        ];

        foreach ($types as $type) {
            EventType::create([
                'name' => $type
            ]);
        }
    }
}
