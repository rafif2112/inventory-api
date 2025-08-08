<?php

namespace Database\Seeders;

use App\Models\SubItem;
use App\Models\UnitItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UnitItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subItems = SubItem::all();
        
        foreach ($subItems as $subItem) {
            $maxUnits = min($subItem->stock, 5);
            
            for ($i = 1; $i <= $maxUnits; $i++) {
                UnitItem::create([
                    'sub_item_id' => $subItem->id,
                    'code_unit' => $this->generateCodeUnit($subItem, $i),
                    'description' => "Unit {$i} dari {$subItem->merk}",
                    'procurement_date' => Carbon::now()->subDays(rand(30, 365)),
                    'status' => rand(0, 1) ? true : false,
                    'condition' => rand(0, 9) < 8 ? true : false,
                    'barcode' => null,
                ]);
            }
        }
    }

    /**
     * Generate code unit berdasarkan sub item dan nomor urut
     */
    private function generateCodeUnit(SubItem $subItem, int $number): string
    {
        $words = explode(' ', $subItem->merk);
        $merkCode = strtoupper(substr($words[0], 0, 2) . substr($words[1] ?? '', 0, 1));
        
        $year = Carbon::now()->year;
        $sequence = str_pad($number, 3, '0', STR_PAD_LEFT);
        $subItemHash = substr($subItem->id, 0, 4);
        
        return "{$merkCode}-{$year}-{$subItemHash}-{$sequence}";
    }
}