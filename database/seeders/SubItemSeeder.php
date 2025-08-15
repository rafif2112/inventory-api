<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\SubItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = Item::all();
        
        $subItems = [
            // Laptop
            [
                'item_name' => 'Laptop',
                'data' => [
                    ['merk' => 'Lenovo ThinkPad', 'stock' => 2, 'unit' => 'pcs', 'major_id' => 1],
                    ['merk' => 'Asus', 'stock' => 2, 'unit' => 'pcs', 'major_id' => 5],
                    ['merk' => 'HP', 'stock' => 1, 'unit' => 'pcs', 'major_id' => 2],
                ]
            ],
            [
                'item_name' => 'Komputer',
                'data' => [
                    ['merk' => 'LG', 'stock' => 1, 'unit' => 'pcs', 'major_id' => 3],
                    ['merk' => 'Samsung', 'stock' => 1, 'unit' => 'pcs', 'major_id' => 4]
                ]
            ]
        ];

        foreach ($subItems as $itemData) {
            $item = $items->where('name', $itemData['item_name'])->first();
            
            if ($item) {
                foreach ($itemData['data'] as $subItemData) {
                    SubItem::create([
                        'item_id' => $item->id,
                        'merk' => $subItemData['merk'],
                        'stock' => $subItemData['stock'],
                        'unit' => $subItemData['unit'],
                        'major_id' => $subItemData['major_id'],
                    ]);
                }
            }
        }
    }
}