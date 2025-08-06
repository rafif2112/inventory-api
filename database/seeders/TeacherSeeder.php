<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Teacher::create([
            'name' => 'Nabil',
            'nip' => '2830948234',
            'telephone' => '428944473',
        ]);
        Teacher::create([
            'name' => 'Arga',
            'nip' => '9824098430',
            'telephone' => '234982734',
        ]);
    }
}
