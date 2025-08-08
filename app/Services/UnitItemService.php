<?php

namespace App\Services;

use App\Models\UnitItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UnitItemService
{

    public function getAllUnitItems()
    {
        return UnitItem::with('subItem')
            ->select('*')
            ->latest()
            ->get();
    }

    public function storeUnitItem(array $data)
    {

        try {
            $filename = 'qrcodes/' . time() . '-' . Str::slug($data['code_unit']) . '.svg';
            
            $qrcodeImage = QrCode::format('svg')
                ->size(300)
                ->generate($data['code_unit']);

            // Simpan file ke storage/app/public/qrcodes/
            Storage::disk('public')->put($filename, $qrcodeImage);

            $newUnitItem = UnitItem::create([
                'sub_item_id'      => $data['sub_item_id'],
                'code_unit'        => $data['code_unit'],
                'qrcode'           => $filename,
                'description'      => $data['description'],
                'procurement_date' => $data['procurement_date'],
                'status' => $data['status'] ?? true,
                'condition' => $data['condition'] ?? true,
            ]);

            return $newUnitItem;
        } catch (\Throwable $e) {
            Log::error('Failed to create unit item: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateUnitItem(UnitItem $unitItem, array $data)
    {
        try {
            $unitItem->update([
                'sub_item_id' => $data['sub_item_id'] ?? $unitItem->sub_item_id,
                'code_unit' => $data['code_unit'] ?? $unitItem->code_unit,
                'description' => $data['description'] ?? $unitItem->description,
                'procurement_date' => $data['procurement_date'] ?? $unitItem->procurement_date,
                'status' => $data['status'] ?? $unitItem->status,
                'condition' => $data['condition'] ?? $unitItem->condition,
            ]);

            return $unitItem;
        } catch (\Throwable $e) {
            Log::error('Failed to update unit item: ' . $e->getMessage());
            throw $e;
        }
    }
}
