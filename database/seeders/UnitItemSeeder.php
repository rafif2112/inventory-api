<?php

namespace Database\Seeders;

use App\Models\SubItem;
use App\Models\UnitItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class UnitItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subItems = SubItem::with('major')->get();

        foreach ($subItems as $subItem) {
            $maxUnits = min($subItem->stock, 5);

            for ($i = 1; $i <= $maxUnits; $i++) {
                $codeUnit = $this->generateCodeUnit($subItem, $i);

                $filename = 'qrcodes/' . time() . '-' . Str::slug($codeUnit) . '.svg';

                $qrcodeImage = QrCode::format('svg')
                    ->size(300)
                    ->generate($codeUnit);

                Storage::disk('public')->put($filename, $qrcodeImage);

                UnitItem::create([
                    'sub_item_id' => $subItem->id,
                    'code_unit' => $codeUnit,
                    'description' => "Unit {$i} dari {$subItem->merk}",
                    'procurement_date' => Carbon::now()->subDays(rand(30, 365)),
                    'status' => rand(0, 1) ? true : false,
                    'condition' => rand(0, 9) < 8 ? true : false,
                    'qrcode' => $filename,
                ]);
            }
        }
    }

    /**
     * Generate code unit berdasarkan major dan merk sub item
     */
    private function generateCodeUnit(SubItem $subItem, int $number): string
    {
        $majorCode = strtoupper($subItem->major->name ?? 'UNK');

        $words = explode(' ', $subItem->merk);
        $merkCode = strtoupper(substr($words[0], 0, 4));

        $sequence = str_pad($number, 2, '0', STR_PAD_LEFT);

        $codeItem = $subItem->item->code_item ?? '0';

        return "{$majorCode}-{$merkCode}-{$codeItem}{$sequence}";
    }
}
