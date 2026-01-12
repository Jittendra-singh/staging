<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class CityStateV2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //$countryId = 1; // Assuming USA's country_id is 1
        
        Schema::disableForeignKeyConstraints();
        DB::table('cities')->truncate();
        DB::table('states')->truncate();
        DB::table('countries')->truncate();
        // Insert USA into countries table
        $countryId = DB::table('countries')->insertGetId([
            'country_name' => 'United States',
            'country_code' => 'US',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $states = [
            ['state_id' => 1, 'state_name' => 'Alabama', 'state_code' => 'AL'],
            ['state_id' => 2, 'state_name' => 'Alaska', 'state_code' => 'AK'],
            ['state_id' => 3, 'state_name' => 'Arizona', 'state_code' => 'AZ'],
            ['state_id' => 4, 'state_name' => 'Arkansas', 'state_code' => 'AR'],
            ['state_id' => 5, 'state_name' => 'California', 'state_code' => 'CA'],
            ['state_id' => 6, 'state_name' => 'Colorado', 'state_code' => 'CO'],
            ['state_id' => 7, 'state_name' => 'Connecticut', 'state_code' => 'CT'],
            ['state_id' => 8, 'state_name' => 'Delaware', 'state_code' => 'DE'],
            ['state_id' => 9, 'state_name' => 'Florida', 'state_code' => 'FL'],
            ['state_id' => 10, 'state_name' => 'Georgia', 'state_code' => 'GA'],
            ['state_id' => 11, 'state_name' => 'Hawaii', 'state_code' => 'HI'],
            ['state_id' => 12, 'state_name' => 'Idaho', 'state_code' => 'ID'],
            ['state_id' => 13, 'state_name' => 'Illinois', 'state_code' => 'IL'],
            ['state_id' => 14, 'state_name' => 'Indiana', 'state_code' => 'IN'],
            ['state_id' => 15, 'state_name' => 'Iowa', 'state_code' => 'IA'],
            ['state_id' => 16, 'state_name' => 'Kansas', 'state_code' => 'KS'],
            ['state_id' => 17, 'state_name' => 'Kentucky', 'state_code' => 'KY'],
            ['state_id' => 18, 'state_name' => 'Louisiana', 'state_code' => 'LA'],
            ['state_id' => 19, 'state_name' => 'Maine', 'state_code' => 'ME'],
            ['state_id' => 20, 'state_name' => 'Maryland', 'state_code' => 'MD'],
            ['state_id' => 21, 'state_name' => 'Massachusetts', 'state_code' => 'MA'],
            ['state_id' => 22, 'state_name' => 'Michigan', 'state_code' => 'MI'],
            ['state_id' => 23, 'state_name' => 'Minnesota', 'state_code' => 'MN'],
            ['state_id' => 24, 'state_name' => 'Mississippi', 'state_code' => 'MS'],
            ['state_id' => 25, 'state_name' => 'Missouri', 'state_code' => 'MO'],
            ['state_id' => 26, 'state_name' => 'Montana', 'state_code' => 'MT'],
            ['state_id' => 27, 'state_name' => 'Nebraska', 'state_code' => 'NE'],
            ['state_id' => 28, 'state_name' => 'Nevada', 'state_code' => 'NV'],
            ['state_id' => 29, 'state_name' => 'New Hampshire', 'state_code' => 'NH'],
            ['state_id' => 30, 'state_name' => 'New Jersey', 'state_code' => 'NJ'],
            ['state_id' => 31, 'state_name' => 'New Mexico', 'state_code' => 'NM'],
            ['state_id' => 32, 'state_name' => 'New York', 'state_code' => 'NY'],
            ['state_id' => 33, 'state_name' => 'North Carolina', 'state_code' => 'NC'],
            ['state_id' => 34, 'state_name' => 'North Dakota', 'state_code' => 'ND'],
            ['state_id' => 35, 'state_name' => 'Ohio', 'state_code' => 'OH'],
            ['state_id' => 36, 'state_name' => 'Oklahoma', 'state_code' => 'OK'],
            ['state_id' => 37, 'state_name' => 'Oregon', 'state_code' => 'OR'],
            ['state_id' => 38, 'state_name' => 'Pennsylvania', 'state_code' => 'PA'],
            ['state_id' => 39, 'state_name' => 'Rhode Island', 'state_code' => 'RI'],
            ['state_id' => 40, 'state_name' => 'South Carolina', 'state_code' => 'SC'],
            ['state_id' => 41, 'state_name' => 'South Dakota', 'state_code' => 'SD'],
            ['state_id' => 42, 'state_name' => 'Tennessee', 'state_code' => 'TN'],
            ['state_id' => 43, 'state_name' => 'Texas', 'state_code' => 'TX'],
            ['state_id' => 44, 'state_name' => 'Utah', 'state_code' => 'UT'],
            ['state_id' => 45, 'state_name' => 'Vermont', 'state_code' => 'VT'],
            ['state_id' => 46, 'state_name' => 'Virginia', 'state_code' => 'VA'],
            ['state_id' => 47, 'state_name' => 'Washington', 'state_code' => 'WA'],
            ['state_id' => 48, 'state_name' => 'West Virginia', 'state_code' => 'WV'],
            ['state_id' => 49, 'state_name' => 'Wisconsin', 'state_code' => 'WI'],
            ['state_id' => 50, 'state_name' => 'Wyoming', 'state_code' => 'WY'],
        ];

        DB::table('states')->insert(array_map(function ($state) use ($countryId) {
            return [
                'state_id' => $state['state_id'],
                'state_name' => $state['state_name'],
                'state_code' => $state['state_code'],
                'country_id' => $countryId,
            ];
        }, $states));

            // state_id => city list
        $cities = [
            ['state_id' => 1,  'city_name' => 'Birmingham'],
            ['state_id' => 1,  'city_name' => 'Huntsville'],
            ['state_id' => 1,  'city_name' => 'Tuscaloosa'],
            ['state_id' => 2,  'city_name' => 'Anchorage'],
            ['state_id' => 2,  'city_name' => 'Fairbanks'],
            ['state_id' => 2,  'city_name' => 'Montgomery'],
            ['state_id' => 5,  'city_name' => 'Los Angeles'],
            ['state_id' => 5,  'city_name' => 'San Francisco'],
            ['state_id' => 6,  'city_name' => 'Denver'],
            ['state_id' => 9,  'city_name' => 'Miami'],
            ['state_id' => 10, 'city_name' => 'Atlanta'],
            ['state_id' => 13, 'city_name' => 'Chicago'],
            ['state_id' => 21, 'city_name' => 'Boston'],
            ['state_id' => 22, 'city_name' => 'Detroit'],
            ['state_id' => 32, 'city_name' => 'New York City'],
            ['state_id' => 32, 'city_name' => 'Buffalo'],
            ['state_id' => 33, 'city_name' => 'Charlotte'],
            ['state_id' => 35, 'city_name' => 'Cleveland'],
            ['state_id' => 37, 'city_name' => 'Portland'],
            ['state_id' => 38, 'city_name' => 'Philadelphia'],
            ['state_id' => 43, 'city_name' => 'Houston'],
            ['state_id' => 43, 'city_name' => 'Dallas'],
            ['state_id' => 46, 'city_name' => 'Richmond'],
            ['state_id' => 47, 'city_name' => 'Seattle'],
            
            
        ];

        DB::table('cities')->insert($cities);
    }
}
