<?php

namespace App\Exports;

use App\Models\UnitItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnitItemExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = UnitItem::query()
            ->select('unit_items.*')
            ->with(['subItem', 'subItem.item', 'subItem.major'])
            ->join('sub_items', 'unit_items.sub_item_id', '=', 'sub_items.id')
            ->join('items', 'sub_items.item_id', '=', 'items.id')
            ->join('majors', 'sub_items.major_id', '=', 'majors.id');

        if ($this->exportType === 'selected' && !empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('unit_items.code_unit', 'ILIKE', '%' . $search . '%')
                    ->orWhere('sub_items.merk', 'ILIKE', '%' . $search . '%')
                    ->orWhere('items.name', 'ILIKE', '%' . $search . '%');
            });
        }

        return $query;
    }

    public function headings(): array
    {
        static $number = 0;
        return [
            'No',
            'Code Unit',
            'Added Date',
            'Merk',
            'Item Name',
            'Major Name',
        ];
    }

    public function map($unitItem): array
    {
        static $counter = 0;
        return [
            ++$counter,
            $unitItem->code_unit,
            $unitItem->created_at,
            $unitItem->subItem->merk ?? 'N/A',
            $unitItem->subItem->item->name ?? 'N/A',
            $unitItem->subItem->major->name ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D3D3D3']]],
        ];
    }
}
