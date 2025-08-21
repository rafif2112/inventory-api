<?php

namespace App\Exports;

use App\Models\ConsumableItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsumableItemExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $exportType;
    protected $selectedIds;
    protected $filters;

    public function __construct($exportType, $selectedIds = [], $filters = [])
    {
        $this->exportType = $exportType;
        $this->selectedIds = $selectedIds;
        $this->filters = $filters;
    }

    public function query()
    {
        $query = ConsumableItem::query()
            ->select('consumable_items.*')
            ->with(['subItem', 'subItem.item', 'subItem.major'])
            ->join('sub_items', 'consumable_items.sub_item_id', '=', 'sub_items.id')
            ->join('items', 'sub_items.item_id', '=', 'items.id')
            ->join('majors', 'sub_items.major_id', '=', 'majors.id');

        if ($this->exportType === 'selected' && !empty($this->selectedIds)) {
            $query->whereIn('consumable_items.id', $this->selectedIds);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('consumable_items.name', 'ILIKE', '%' . $search . '%');
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Code Unit',
            'Added Date',
            'Merk',
            'Item Name',
            'Major Name',
        ];
    }

    public function map($consumableItem): array
    {
        static $number = 0;
        return [
            ++$number,
            $consumableItem->code_unit,
            $consumableItem->created_at,
            $consumableItem->subItem->merk ?? 'N/A',
            $consumableItem->subItem->item->name ?? 'N/A',
            $consumableItem->subItem->major->name ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['color' => ['argb' => 'D3D3D3']]],
        ];
    }
}
