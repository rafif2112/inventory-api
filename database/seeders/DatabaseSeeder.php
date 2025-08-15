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
        Major::create([
            'name' => 'DKV',
            'color' => '#3357FF',
        ]);
        Major::create([
            'name' => 'TJKT',
            'color' => '#FF33A1',
        ]);
        Major::create([
            'name' => 'MPLB',
            'color' => '#FFB833',
        ]);
        Major::create([
            'name' => 'KLN',
            'color' => '#33FFA1',
        ]);
        Major::create([
            'name' => 'HTL',
            'color' => '#A133FF',
        ]);
        Major::create([
            'name' => 'PMN',
            'color' => '#FF5733',
        ]);

        User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'role' => 'superadmin',
            'password' => bcrypt('password'), // password
        ]);

        User::create([
            'name' => 'Kaprog PPLG',
            'username' => 'kaprogpplg',
            'role' => 'admin',
            'password' => bcrypt('password'),
            'major_id' => 1
        ]);

        User::create([
            'name' => 'Kepala Lab PPLG',
            'username' => 'labpplg',
            'role' => 'user',
            'password' => bcrypt('password'),
            'major_id' => 1
        ]);

        $this->call([
            ItemSeeder::class,
            SubItemSeeder::class,
            UnitItemSeeder::class,
        ]);
    }
}
