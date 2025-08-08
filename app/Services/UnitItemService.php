<?php

namespace App\Services;

use App\Models\UnitItem;
use App\Models\SubItem;
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

    private function generateCodeUnit($subItem, int $number): string
    {
        $majorCode = strtoupper($subItem->major->name ?? 'UNK');

        $words = explode(' ', $subItem->merk);
        $merkCode = strtoupper(substr($words[0], 0, 3));

        $sequence = str_pad($number, 3, '0', STR_PAD_LEFT);

        return "{$majorCode}-{$merkCode}-{$sequence}";
    }

    public function storeUnitItem(array $data)
    {

        try {
            $subItem = SubItem::with('major')->find($data['sub_item_id']);
            
            $lastUnitItem = UnitItem::where('sub_item_id', $data['sub_item_id'])
                ->orderBy('id', 'desc')
                ->first();
            
            $sequenceNumber = $lastUnitItem ? 
                intval(substr($lastUnitItem->code_unit, -3)) + 1 : 1;
            
            $codeUnit = $this->generateCodeUnit($subItem, $sequenceNumber);
            
            $filename = 'qrcodes/' . time() . '-' . Str::slug($codeUnit) . '.svg';
            
            $qrcodeImage = QrCode::format('svg')
                ->size(300)
                ->generate($codeUnit);

            // Simpan file ke storage/app/public/qrcodes/
            Storage::disk('public')->put($filename, $qrcodeImage);

            $newUnitItem = UnitItem::create([
                'sub_item_id'      => $data['sub_item_id'],
                'code_unit'        => $codeUnit,
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
                // 'code_unit' => $data['code_unit'] ?? $unitItem->code_unit,
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