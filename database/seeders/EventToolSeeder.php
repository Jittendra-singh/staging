<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class EventToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tools')->truncate();
        DB::table('tools')->insert([
            ['id' => 1, 'should_bring' => 0 , 'name' => 'Closed Toed Shoes', 'created_at' => now()],
            ['id' => 2, 'should_bring' => 0 , 'name' => 'Water in a Reusable Bottle', 'created_at' => now()],
            ['id' => 3, 'should_bring' => 1 , 'name' => 'Sunscreen', 'created_at' => now()],
            ['id' => 4, 'should_bring' => 0 , 'name' => 'Insect Repellent', 'created_at' => now()],
            ['id' => 5, 'should_bring' => 0 , 'name' => 'Garbage Bags', 'created_at' => now()],
            ['id' => 6, 'should_bring' => 0 , 'name' => 'Gloves', 'created_at' => now()],
            ['id' => 7, 'should_bring' => 1 , 'name' => 'Hat & Sunglasses', 'created_at' => now()],
            ['id' => 8, 'should_bring' => 0 , 'name' => 'Water Refills', 'created_at' => now()],
            ['id' => 9, 'should_bring' => 1 , 'name' => 'Pickers', 'created_at' => now()],
            ['id' => 10, 'should_bring' => 0 , 'name' => 'Buckets', 'created_at' => now()],
        ]);
    }
}
