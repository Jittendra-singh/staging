<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('event_categories')->truncate();
        DB::table('event_categories')->insert([
            ['id' => 1, 'slug' => 'beach' , 'name' => 'Beach', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'slug' => 'neightborhood' , 'name' => 'Neighborhood', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'slug' => 'paddle' , 'name' => 'Paddle / Boat', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'slug' => 'scuba' , 'name' => 'Scuba / Underwater', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'slug' => 'shoreline' , 'name' => 'Shoreline / Mixed Terrain', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
