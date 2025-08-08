<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Laptop'],
            ['name' => 'Komputer'],
            ['name' => 'Monitor'],
            ['name' => 'Keyboard'],
            ['name' => 'Mouse'],
            ['name' => 'Printer'],
            ['name' => 'Speaker'],
            ['name' => 'Headset'],
            ['name' => 'Webcam'],
            ['name' => 'Proyektor'],
            ['name' => 'Kabel HDMI'],
            ['name' => 'Router'],
            ['name' => 'Switch'],
            ['name' => 'Tablet'],
            ['name' => 'Smartphone'],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}