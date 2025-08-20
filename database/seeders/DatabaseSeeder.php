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
            'color' => '#A2C5FF',
        ]);
        Major::create([
            'name' => 'DKV',
            'color' => '#FFD6A1',
        ]);
        Major::create([
            'name' => 'TJKT',
            'color' => '#FFF3A4',
        ]);
        Major::create([
            'name' => 'MPLB',
            'color' => '#FF484B',
        ]);
        Major::create([
            'name' => 'KLN',
            'color' => '#D967CA',
        ]);
        Major::create([
            'name' => 'HTL',
            'color' => '#42A531',
        ]);
        Major::create([
            'name' => 'PMN',
            'color' => '#877E31',
        ]);

        User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'role' => 'superadmin',
            'password' => bcrypt('password'),
            'major_id' => 1
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
