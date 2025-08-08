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
                    ['merk' => 'Lenovo ThinkPad', 'stock' => 10, 'unit' => 'unit', 'major_id' => 1],
                ]
            ],
            // Komputer
            [
                'item_name' => 'Komputer',
                'data' => [
                    ['merk' => 'HP Desktop', 'stock' => 15, 'unit' => 'unit', 'major_id' => 3],
                ]
            ],
            // Monitor
            [
                'item_name' => 'Monitor',
                'data' => [
                    ['merk' => 'Samsung 24 inch', 'stock' => 25, 'unit' => 'unit', 'major_id' => 1],
                ]
            ],
            // Keyboard
            [
                'item_name' => 'Keyboard',
                'data' => [
                    ['merk' => 'Corsair K70', 'stock' => 15, 'unit' => 'unit', 'major_id' => 1],
                ]
            ],
            // Mouse
            [
                'item_name' => 'Mouse',
                'data' => [
                    ['merk' => 'Steelseries Rival', 'stock' => 20, 'unit' => 'unit', 'major_id' => 1],
                ]
            ],
            // Printer
            [
                'item_name' => 'Printer',
                'data' => [
                    ['merk' => 'Canon Pixma', 'stock' => 8, 'unit' => 'unit', 'major_id' => 1],
                ]
            ],
            // Speaker
            [
                'item_name' => 'Speaker',
                'data' => [
                    ['merk' => 'JBL Clip 3', 'stock' => 15, 'unit' => 'unit', 'major_id' => 2],
                ]
            ],
            // Headset
            [
                'item_name' => 'Headset',
                'data' => [
                    ['merk' => 'HyperX Cloud II', 'stock' => 20, 'unit' => 'unit', 'major_id' => 1],
                ]
            ],
            // Webcam
            [
                'item_name' => 'Webcam',
                'data' => [
                    ['merk' => 'Logitech C920', 'stock' => 12, 'unit' => 'unit', 'major_id' => 1],
                ]
            ],
            // Proyektor
            [
                'item_name' => 'Proyektor',
                'data' => [
                    ['merk' => 'Epson EB-X41', 'stock' => 5, 'unit' => 'unit', 'major_id' => 1],
                ]
            ],
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