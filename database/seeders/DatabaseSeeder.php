<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Major;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Major::create([
            'name' => 'PPLG',
            'color' => '#FF5733',
        ]);

        User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'role' => 'superadmin',
            'password' => bcrypt('password'), // password
            'major_id' => 1,
        ]);
    }
}
