<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeacherExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = Teacher::query()
            ->select('teachers.*');

        if ($this->exportType === 'selected' && !empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where('name', 'ILIKE', '%' . $search . '%')
                ->orWhere('nip', 'ILIKE', '%' . $search . '%');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Name',
            'NIP',
            'Telephone',
        ];
    }

    public function map($teacher): array
    {
        static $number = 0;
        return [
            ++$number,
            $teacher->name,
            $teacher->nip,
            $teacher->telephone,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D3D3D3']]],
        ];
    }
}
