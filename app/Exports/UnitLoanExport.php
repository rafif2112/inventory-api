<?php

namespace App\Exports;

use App\Models\UnitLoan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnitLoanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $exportType;
    protected $selectedIds;
    protected $filters;
    protected $user;

    public function __construct($exportType, $selectedIds = [], $filters = [], $user = null)
    {
        $this->exportType = $exportType;
        $this->selectedIds = $selectedIds;
        $this->filters = $filters;
        $this->user = $user;
    }

    public function query()
    {
        $query = UnitLoan::query()
            ->select('unit_loans.*')
            ->with(['unitItem', 'student', 'student.major', 'teacher', 'unitItem.subItem', 'unitItem.subItem.item'])
            ->join('unit_items', 'unit_loans.unit_item_id', '=', 'unit_items.id')
            ->join('sub_items', 'unit_items.sub_item_id', '=', 'sub_items.id')
            ->join('items', 'sub_items.item_id', '=', 'items.id')
            ->join('majors', 'sub_items.major_id', '=', 'majors.id')
            ->leftJoin('students', 'unit_loans.student_id', '=', 'students.id')
            ->leftJoin('teachers', 'unit_loans.teacher_id', '=', 'teachers.id');

        if (isset($this->filters['type'])) {
            if ($this->filters['type'] === 'returning') {
                $query->where('unit_loans.status', false);
            } else {
                $query->where('unit_loans.status', true);
            }
        }

        if ($this->user->role !== 'superadmin') {
            $query->where('majors.id', $this->user->major_id);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('unit_items.code_unit', 'ILIKE', '%' . $search . '%')
                    ->orWhere('sub_items.merk', 'ILIKE', '%' . $search . '%')
                    ->orWhere('items.name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('students.name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('teachers.name', 'ILIKE', '%' . $search . '%');
            });
        }

        if (!empty($this->filters['sort_by_type'])) {
            $query->orderBy('items.name', $this->filters['sort_by_type']);
        }

        if (!empty($this->filters['sort_by_time'])) {
            $query->orderBy('unit_loans.borrowed_at', $this->filters['sort_by_time']);
        }

        if ($this->exportType === 'selected' && !empty($this->selectedIds)) {
            $query->whereIn('unit_loans.id', $this->selectedIds);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Item Type',
            'Unit Code',
            'Brand',
            'Lender Name',
            'Borrower Name',
            'Rayon',
            'Major',
            'Borrowed Date',
            'Returned Date',
            'Purpose',
            'Room',
            'Status',
            'Guarantee',
        ];
    }

    public function map($unitLoan): array
    {
        static $number = 0;
        return [
            ++$number,
            $unitLoan->unitItem->subItem->item->name ?? 'N/A',
            $unitLoan->unitItem->code_unit ?? 'N/A',
            $unitLoan->unitItem->subItem->merk ?? 'N/A',
            $unitLoan->borrowed_by ?? 'N/A',
            $unitLoan->student ? $unitLoan->student->name : ($unitLoan->teacher ? $unitLoan->teacher->name : 'N/A'),
            $unitLoan->student ? $unitLoan->student->rayon : 'N/A',
            $unitLoan->student ? $unitLoan->student->major->name : 'N/A',
            $unitLoan->borrowed_at ? \Carbon\Carbon::parse($unitLoan->borrowed_at)->format('d M Y H:i') : 'N/A',
            $unitLoan->returned_at ? \Carbon\Carbon::parse($unitLoan->returned_at)->format('d M Y H:i') : 'Not Returned',
            $unitLoan->purpose ?? 'N/A',
            $unitLoan->room ?? 'N/A',
            $unitLoan->status ? 'Borrowed' : 'Returned',
            $unitLoan->guarantee ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D3D3D3']]],
        ];
    }
}
