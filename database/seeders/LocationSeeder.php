<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            ['location_code' => 'LOC-A', 'location_name' => 'อาคาร A', 'building' => 'Building A', 'floor' => '1', 'zone' => 'A1'],
            ['location_code' => 'LOC-B', 'location_name' => 'อาคาร B', 'building' => 'Building B', 'floor' => '1', 'zone' => 'B1'],
            ['location_code' => 'LOC-C', 'location_name' => 'อาคาร C', 'building' => 'Building C', 'floor' => '2', 'zone' => 'C2'],
            ['location_code' => 'LOC-D', 'location_name' => 'อาคาร D', 'building' => 'Building D', 'floor' => '1', 'zone' => 'D1'],
            ['location_code' => 'LOC-E', 'location_name' => 'อาคาร E', 'building' => 'Building E', 'floor' => '3', 'zone' => 'E3'],
        ];

        foreach ($locations as $loc) {
            Location::create($loc);
        }
    }
}
