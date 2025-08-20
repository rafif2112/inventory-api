<?php

namespace App\Services;

use App\Models\UnitItem;
use App\Models\SubItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UnitItemService
{

    public function getAllUnitItems($user, $search, $sortDate, $sortType, $sortCondition, $sortMajor)
    {
        $query = UnitItem::query()
            ->select('unit_items.*')
            ->with('subItem', 'subItem.item', 'subItem.major')
            ->join('sub_items', 'unit_items.sub_item_id', '=', 'sub_items.id')
            ->join('items', 'sub_items.item_id', '=', 'items.id')
            ->join('majors', 'sub_items.major_id', '=', 'majors.id')
            ->when($sortCondition, function ($q) use ($sortCondition) {
                $q->orderBy('unit_items.condition', $sortCondition);
            })
            ->when($sortDate, function ($q) use ($sortDate) {
                $q->orderBy('unit_items.procurement_date', $sortDate);
            })
            ->when($sortType, function ($q) use ($sortType) {
                $q->orderBy('items.name', $sortType);
            })
            ->when($sortMajor, function ($q) use ($sortMajor) {
                $q->orderBy('majors.name', $sortMajor);
            })
            ->latest();

        if ($search) {
            $query->where('unit_items.code_unit', 'ILIKE', '%' . $search . '%')
                ->orWhere('sub_items.merk', 'ILIKE', '%' . $search . '%')
                ->orWhere('items.name', 'ILIKE', '%' . $search . '%');
        }

        if ($user->role != 'superadmin' && !empty($user->role)) {
            $query->where('sub_items.major_id', $user->major_id);
        }

        return $query->paginate(10);
    }


    private function generateCodeUnit($subItem, int $number): string
    {
        $majorCode = strtoupper($subItem->major->name ?? 'UNK');

        $words = explode(' ', $subItem->merk);
        $merkCode = strtoupper(substr($words[0], 0, 4));

        $sequence = str_pad($number, 2, '0', STR_PAD_LEFT);

        $codeItem = $subItem->item->code_item ?? '0';

        return "{$majorCode}-{$merkCode}-{$codeItem}{$sequence}";
    }

    public function storeUnitItem(array $data)
    {
        try {
            $subItem = SubItem::with('major')
                ->where('item_id', $data['item_id'])
                ->where(function ($query) use ($data) {
                    $query->whereRaw("LOWER(REPLACE(merk, ' ', '')) = ?", [
                        strtolower(str_replace(' ', '', $data['merk']))
                    ]);
                })->first();

            if (!$subItem) {
                $newSubItem = SubItem::create([
                    'merk' => $data['merk'],
                    'item_id' => $data['item_id'],
                    'stock' => 1,
                    'major_id' => Auth::user()->major_id,
                ]);

                $subItem = $newSubItem;
            }

            $merkWords = explode(' ', $data['merk']);
            $firstMerkWord = $merkWords[0];

            $similarSubItems = SubItem::where(function ($query) use ($firstMerkWord) {
                    $query->where('merk', 'ILIKE', $firstMerkWord . '%');
                })
                ->where('item_id', $data['item_id'])
                ->pluck('id');

            if ($similarSubItems->isEmpty()) {
                $sequenceNumber = 1;
            } else {
                $lastUnitItem = UnitItem::whereIn('sub_item_id', $similarSubItems)
                    ->orderBy('id', 'desc')
                    ->first();

                $sequenceNumber = $lastUnitItem ?
                    intval(substr($lastUnitItem->code_unit, -2)) + 1 : 1;
            }

            $codeUnit = $this->generateCodeUnit($subItem, $sequenceNumber);

            $filename = 'qrcodes/' . time() . '-' . Str::slug($codeUnit) . '.svg';

            $qrcodeImage = QrCode::format('svg')
                ->size(300)
                ->generate($codeUnit);

            Storage::disk('public')->put($filename, $qrcodeImage);

            $newUnitItem = UnitItem::create([
                'sub_item_id'      => $subItem->id,
                'code_unit'        => $codeUnit,
                'qrcode'           => $filename,
                'description'      => $data['description'],
                'procurement_date' => $data['procurement_date'],
                'status' => $data['status'] ?? true,
                'condition' => $data['condition'] ?? true,
            ]);

            $subItem->increment('stock');

            return $newUnitItem;
        } catch (\Throwable $e) {
            Log::error('Failed to create unit item: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateUnitItem(UnitItem $unitItem, array $data)
    {
        try {
            if (isset($data['item_id']) && isset($data['merk'])) {
                $subItem = SubItem::with('major')
                    ->where('item_id', $data['item_id'])
                    ->where(function ($query) use ($data) {
                        $query->whereRaw("LOWER(REPLACE(merk, ' ', '')) = ?", [
                            strtolower(str_replace(' ', '', $data['merk']))
                        ]);
                    })->first();

                if (!$subItem) {
                    $newSubItem = SubItem::create([
                        'merk' => $data['merk'],
                        'item_id' => $data['item_id'],
                        'stock' => 1,
                        'major_id' => Auth::user()->major_id,
                    ]);

                    $subItem = $newSubItem;
                }

                if ($unitItem->sub_item_id != $subItem->id) {
                    if ($unitItem->qrcode && Storage::disk('public')->exists($unitItem->qrcode)) {
                        Storage::disk('public')->delete($unitItem->qrcode);
                    }

                    $lastUnitItem = UnitItem::where('sub_item_id', $subItem->id)
                        ->orderBy('id', 'desc')
                        ->first();

                    $sequenceNumber = $lastUnitItem ?
                        intval(substr($lastUnitItem->code_unit, -3)) + 1 : 1;

                    $codeUnit = $this->generateCodeUnit($subItem, $sequenceNumber);

                    $filename = 'qrcodes/' . time() . '-' . Str::slug($codeUnit) . '.svg';

                    $qrcodeImage = QrCode::format('svg')
                        ->size(300)
                        ->generate($codeUnit);

                    Storage::disk('public')->put($filename, $qrcodeImage);

                    $data['sub_item_id'] = $subItem->id;
                    $data['code_unit'] = $codeUnit;
                    $data['qrcode'] = $filename;
                }
            }

            $unitItem->update([
                'sub_item_id' => $data['sub_item_id'] ?? $unitItem->sub_item_id,
                'code_unit' => $data['code_unit'] ?? $unitItem->code_unit,
                'qrcode' => $data['qrcode'] ?? $unitItem->qrcode,
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
