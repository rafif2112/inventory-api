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
            ->join('majors', 'consumable_items.major_id', '=', 'majors.id');

        if ($this->exportType === 'selected' && !empty($this->selectedIds)) {
            $query->whereIn('consumable_items.id', $this->selectedIds);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
            $q->where('consumable_items.name', 'ILIKE', '%' . $search . '%')
              ->orWhere('majors.name', 'ILIKE', '%' . $search . '%');
            });
        }

        if (!empty($this->filters['sort_type'])) {
            $query->orderBy('consumable_items.name', $this->filters['sort_type']);
        }

        if (!empty($this->filters['sort_quantity'])) {
            $query->orderBy('consumable_items.quantity', $this->filters['sort_quantity']);
        }

        if (!empty($this->filters['sort_major'])) {
            $query->orderBy('majors.name', $this->filters['sort_major']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Item Name',
            'Quantity',
            'Unit',
            'Major Name',
        ];
    }

    public function map($consumableItem): array
    {
        static $number = 0;
        return [
            ++$number,
            $consumableItem->name,
            $consumableItem->quantity,
            $consumableItem->unit,
            $consumableItem->major->name ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['color' => ['argb' => 'D3D3D3']]],
        ];
    }
}
