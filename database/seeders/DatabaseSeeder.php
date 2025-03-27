<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            $admin = [
                'name' => 'Admin',
                'email' => 'admin@yopmail.com',
                'role' => 'admin',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'email_verified_at' => now(),
            ],
            $user = [
                'name' => 'User',
                'email' => 'user@yopmail.com',
                'role' => 'user',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'email_verified_at' => now(),
            ],
        ]);

        Sale::factory(50)->create();
    }
}
