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
            'icon' => 'pplg.png',
            'color' => '#A2C5FF',
        ]);
        Major::create([
            'name' => 'DKV',
            'icon' => 'dkv.jpg',
            'color' => '#FFD6A1',
        ]);
        Major::create([
            'name' => 'TJKT',
            'icon' => 'tjkt.jpg',
            'color' => '#FFF3A4',
        ]);
        Major::create([
            'name' => 'MPLB',
            'icon' => 'mplb.png',
            'color' => '#FF484B',
        ]);
        Major::create([
            'name' => 'KLN',
            'icon' => 'kln.jpg',
            'color' => '#D967CA',
        ]);
        Major::create([
            'name' => 'HTL',
            'icon' => 'htl.jpg',
            'color' => '#42A531',
        ]);
        Major::create([
            'name' => 'PMN',
            'icon' => 'pmn.jpg',
            'color' => '#877E31',
        ]);

        $this->call([
            UserSeeder::class,
        ]);
    }
}
