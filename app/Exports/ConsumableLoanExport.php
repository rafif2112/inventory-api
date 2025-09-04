<?php

namespace App\Exports;

use App\Models\ConsumableLoan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsumableLoanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = ConsumableLoan::query()
            ->with(['consumableItem', 'student', 'student.major', 'teacher'])
            ->join('consumable_items', 'consumable_loans.consumable_item_id', '=', 'consumable_items.id')
            ->leftJoin('majors', 'consumable_items.major_id', '=', 'majors.id')
            ->leftJoin('students', 'consumable_loans.student_id', '=', 'students.id')
            ->leftJoin('teachers', 'consumable_loans.teacher_id', '=', 'teachers.id');

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('consumable_items.name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('students.name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('teachers.name', 'ILIKE', '%' . $search . '%');
            });
        }

        if ($this->user->role !== 'superadmin') {
            $query->where('majors.id', $this->user->major_id);
        }

        // Apply sorting
        if (!empty($this->filters['sort_type'])) {
            $query->orderBy('consumable_items.name', $this->filters['sort_type']);
        }

        if (!empty($this->filters['sort_quantity'])) {
            $query->orderBy('consumable_loans.quantity', $this->filters['sort_quantity']);
        }

        // Apply export type filter
        if ($this->exportType === 'selected' && !empty($this->selectedIds)) {
            $query->whereIn('consumable_loans.id', $this->selectedIds);
        }

        return $query->select('consumable_loans.*');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Item Name',
            'Quantity',
            'Unit',
            'Lender Name',
            'Borrower Name',
            'Borrowed Date',
            'Purpose',
            'Major',
        ];
    }

    public function map($consumableLoan): array
    {
        return [
            $consumableLoan->id,
            $consumableLoan->consumableItem->name ?? 'N/A',
            $consumableLoan->quantity ?? 0,
            $consumableLoan->consumableItem->unit ?? 'N/A',
            $consumableLoan->borrowed_by ?? 'N/A',
            $consumableLoan->student ? $consumableLoan->student->name : ($consumableLoan->teacher ? $consumableLoan->teacher->name : 'N/A'),
            $consumableLoan->borrowed_at ? \Carbon\Carbon::parse($consumableLoan->borrowed_at)->format('d M Y H:i') : 'N/A',
            $consumableLoan->purpose ?? 'N/A',
            $consumableLoan->student ? $consumableLoan->student->major->name ?? 'N/A' : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D3D3D3']]],
        ];
    }
}