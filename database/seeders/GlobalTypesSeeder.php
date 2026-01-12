<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class GlobalTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('global_types')->truncate();
        \DB::table('global_types')->insert([
            [
            'name' => 'Tag',
            'slug' => 'tags',
            'table_name' => 'tags',
            'class_name' => 'App\Models\Tag',
            'status' => '1',
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'Event Tools',
            'slug' => 'tools',
            'table_name' => 'tools',
            'class_name' => 'App\Models\Tool',
            'status' => '1',
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'Event Categories',
            'slug' => 'categories',
            'table_name' => 'event_categories',
            'class_name' => 'App\Models\EventCategory',
            'status' => '1',
            'created_at' => now(),
            'updated_at' => now(),
            ],
        ]);
    }
}
