<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = Student::query()
            ->select('students.*')
            ->with('major')
            ->join('majors', 'students.major_id', '=', 'majors.id');

        if ($this->exportType === 'selected' && !empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where('name', 'ILIKE', '%' . $search . '%')
                ->orWhere('nis', 'ILIKE', '%' . $search . '%');
        }

        if (!empty($this->filters['sort_major'])) {
            $query->orderBy('major.name', $this->filters['sort_major']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Name',
            'NIS',
            'Rayon',
            'Major',
        ];
    }

    public function map($student): array
    {
        static $counter = 0;
        return [
            ++$counter,
            $student->name,
            $student->nis,
            $student->rayon ?? 'N/A',
            $student->major->name ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D3D3D3']]],
        ];
    }
}
