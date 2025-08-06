<?php

namespace App\Services;

use App\Models\UnitItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnitItemService{

    public function getAllUnitItems()
    {
        return UnitItem::with('subItem')
            ->select('*')
            ->latest()
            ->get();
    }

    public function storeUnitItem(array $data){

        try{
            $newUnitItem = UnitItem::create([
                'sub_item_id' => $data['sub_item_id'],
                'code_unit' => $data['code_unit'],
                'description' => $data['description'],
                'procurement_date' => $data['procurement_date'],
                'status' => $data['status'] ?? false,
                'condition' => $data['condition'] ?? false,
            ]);

            return $newUnitItem;
        }catch (\Throwable $e){
            Log::error('Failed to create unit item: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateUnitItem(UnitItem $unitItem, array $data)
    {
        try {
            $unitItem->update([
                'sub_item_id' => $data['sub_item_id'],
                'code_unit' => $data['code_unit'],
                'description' => $data['description'],
                'procurement_date' => $data['procurement_date'],
                'status' => $data['status'] ?? false,
                'condition' => $data['condition'] ?? false,
            ]);

            return $unitItem;
        } catch (\Throwable $e) {
            Log::error('Failed to update unit item: ' . $e->getMessage());
            throw $e;
        }
    }

    
}
