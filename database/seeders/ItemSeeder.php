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
            ['name' => 'Laptop', 'code_item' => 1],
            ['name' => 'Komputer', 'code_item' => 2],
            ['name' => 'Monitor', 'code_item' => 3],
            ['name' => 'Keyboard', 'code_item' => 4],
            ['name' => 'Mouse', 'code_item' => 5],
            ['name' => 'Printer', 'code_item' => 6],
            ['name' => 'Speaker', 'code_item' => 7],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}