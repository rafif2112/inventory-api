<?php

namespace App\Exports;

use App\Models\SubItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubItemExport implements WithHeadings, WithStyles, WithMapping, FromQuery, ShouldAutoSize
{
    protected $exportType;
    protected $selectedIds;
    protected $filters;

    public function __construct($exportType, $selectedIds, $filters)
    {
        $this->exportType = $exportType;
        $this->selectedIds = $selectedIds;
        $this->filters = $filters;
    }

    public function query()
    {
        $query = SubItem::query()
            ->select('sub_items.*')
            ->with(['item', 'major'])
            ->leftJoin('majors', 'sub_items.major_id', '=', 'majors.id');

        if ($this->exportType === 'selected' && !empty($this->selectedIds)) {
            $query->whereIn('sub_items.id', $this->selectedIds);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where('sub_items.merk', 'ILIKE', '%' . $search . '%');  
        }

        if (!empty($this->filters['sort_merk'])) {
            $query->orderBy('sub_items.merk', $this->filters['sort_merk']);
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
            'Type',
            'Merk',
            'Major',
        ];
    }

    public function map($row): array
    {
        static $counter = 0;
        return [
            ++$counter,
            $row->item->name,
            $row->merk,
            $row->major->name,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D3D3D3']]],
        ];
    }
}
