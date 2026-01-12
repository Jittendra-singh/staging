<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class CitiesStateSeeder extends Seeder
{
    /**
    * Run the database seeds.
    */
    public function run(): void
    {
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();
            DB::table('countries')->truncate();
            DB::table('states')->truncate();
            DB::table('cities')->truncate();
        Schema::enableForeignKeyConstraints();
        // Insert USA into countries table
        $countryId = DB::table('countries')->insertGetId([
            'country_name' => 'United States',
            'country_code' => 'US',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $states = [
            ['state_id' => 1 , 'state_name' => 'Alabama', 'state_code' => 'AL' , 'country_id' => 1],
            ['state_id' => 2 , 'state_name' => 'Alaska', 'state_code' => 'AK' , 'country_id' => 1],
        ];
        foreach ($states as $state) {
            DB::table('states')->insertGetId([
                'state_name' => $state['state_name'],
                'country_id' => 1,
                'state_code' => $state['state_code'],
                'created_at' => now(),
                'updated_at' => now(), 
            ]);
        }
        $cities = [
            // Alabama
            ['city_id' => 1 ,'city_name' => 'Birmingham',  'state_id' => 1 , 'created_at' => now(), 'updated_at' => now()],
            ['city_id' => 2 ,'city_name' => 'Huntsville',  'state_id' => 1 , 'created_at' => now(), 'updated_at' => now()],
            ['city_id' => 4 ,'city_name' => 'Montgomery',  'state_id' => 1 , 'created_at' => now(), 'updated_at' => now()],
            ['city_id' => 5 ,'city_name' => 'Tuscaloosa',  'state_id' => 1 , 'created_at' => now(), 'updated_at' => now()],
            ['city_id' => 6 ,'city_name' => 'Montgomery',  'state_id' => 1 , 'created_at' => now(), 'updated_at' => now()],
            ['city_id' => 7 ,'city_name' => 'Mobile'    ,  'state_id' => 1 , 'created_at' => now(), 'updated_at' => now()],
            // Alaska
            ['city_id' => 8 ,'city_name' => 'Anchorage',  'state_id' => 2 , 'created_at' => now(), 'updated_at' => now()],
            ['city_id' => 9 ,'city_name' => 'Fairbanks',  'state_id' => 2 , 'created_at' => now(), 'updated_at' => now()],
        ];
        foreach ($cities as $city) {
            DB::table('cities')->insertGetId([
                'city_id' => $city['city_id'],
                'city_name' => $city['city_name'],
                'state_id' => $city['state_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
