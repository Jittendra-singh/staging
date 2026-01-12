<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
class SuperAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::truncate();
        DB::table('roles')->insert([
            'role_name' => 'Super Admin',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::truncate();
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@volunteer.com',
            'is_active' => 1,
            'role_id' => 1,
            'password' => Hash::make('12345678'),  
        ]);
    }
}
