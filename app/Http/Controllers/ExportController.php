<?php

namespace App\Http\Controllers;

use App\Exports\UnitLoanExport;
use App\Exports\ConsumableLoanExport;
use App\Exports\StudentExport;
use App\Exports\TeacherExport;
use App\Exports\UnitItemExport;
use App\Exports\ConsumableItemExport;
use App\Exports\ItemExport;
use App\Exports\SubItemExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Export Unit Loan data
     */
    public function exportUnitLoan(Request $request)
    {
        $request->validate([
            'export' => 'required|in:selected,all',
            'data' => 'array|required_if:export,selected',
            'data.*' => 'uuid|exists:unit_loans,id',
            'type' => 'string|in:borrowing,returning',
            'search' => 'string|nullable',
            'sort_by_type' => 'string|nullable|in:asc,desc',
            'sort_by_time' => 'string|nullable|in:asc,desc',
        ]);

        $user = Auth::user();
        $exportType = $request->input('export');
        $selectedIds = $request->input('data', []);
        $filters = [
            'type' => $request->input('type', 'borrowing'),
            'search' => $request->input('search'),
            'sort_by_type' => $request->input('sort_by_type'),
            'sort_by_time' => $request->input('sort_by_time'),
        ];

        $filename = 'unit_loan_' . $filters['type'] . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new UnitLoanExport($exportType, $selectedIds, $filters, $user),
            $filename
        );
    }

    /**
     * Export Consumable Loan data
     */
    public function exportConsumableLoan(Request $request)
    {
        $request->validate([
            'export' => 'required|in:selected,all',
            'data' => 'array|required_if:export,selected',
            'data.*' => 'uuid|exists:consumable_loans,id',
            'search' => 'string|nullable',
            'sort_type' => 'string|nullable|in:asc,desc',
            'sort_quantity' => 'string|nullable|in:asc,desc',
        ]);

        $user = Auth::user();
        $exportType = $request->input('export');
        $selectedIds = $request->input('data', []);
        $filters = [
            'search' => $request->input('search'),
            'sort_type' => $request->input('sort_type'),
            'sort_quantity' => $request->input('sort_quantity'),
        ];

        $filename = 'consumable_loan_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new ConsumableLoanExport($exportType, $selectedIds, $filters, $user),
            $filename
        );
    }

    /**
     * Export Student data
     */
    public function exportStudents(Request $request)
    {
        $request->validate([
            'export' => 'required|in:selected,all',
            'data' => 'array|required_if:export,selected',
            'data.*' => 'uuid|exists:students,id',
            'search' => 'string|nullable',
            'sort_major' => 'string|nullable|in:asc,desc',
        ]);

        $exportType = $request->input('export');
        $selectedIds = $request->input('data', []);
        $filters = [
            'search' => $request->input('search'),
            'sort_major' => $request->input('sort_major'),
        ];

        $filename = 'students_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new StudentExport($exportType, $selectedIds, $filters),
            $filename
        );
    }

    /**
     * Export Teacher data
     */
    public function exportTeachers(Request $request)
    {
        $request->validate([
            'export' => 'required|in:selected,all',
            'data' => 'array|required_if:export,selected',
            'data.*' => 'uuid|exists:teachers,id',
            'search' => 'string|nullable',
        ]);

        $exportType = $request->input('export');
        $selectedIds = $request->input('data', []);
        $filters = [
            'search' => $request->input('search'),
        ];

        $filename = 'teachers_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new TeacherExport($exportType, $selectedIds, $filters),
            $filename
        );
    }

    /**
     * Export Unit Items data
     */
    public function exportUnitItems(Request $request)
    {
        $request->validate([
            'export' => 'required|in:selected,all',
            'data' => 'array|required_if:export,selected',
            'data.*' => 'uuid|exists:unit_items,id',
            'search' => 'string|nullable',
            'sort_date' => 'string|nullable|in:asc,desc',
            'sort_type' => 'string|nullable|in:asc,desc',
            'sort_condition' => 'string|nullable|in:asc,desc',
            'sort_major' => 'string|nullable|in:asc,desc',
        ]);

        $user = Auth::user();
        $exportType = $request->input('export');
        $selectedIds = $request->input('data', []);
        $filters = [
            'search' => $request->input('search'),
            'sort_date' => $request->input('sort_date'),
            'sort_type' => $request->input('sort_type'),
            'sort_condition' => $request->input('sort_condition'),
            'sort_major' => $request->input('sort_major'),
        ];

        $filename = 'unit_items_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new UnitItemExport($exportType, $selectedIds, $filters, $user),
            $filename
        );
    }

    /**
     * Export Consumable Items data
     */
    public function exportConsumableItems(Request $request)
    {
        $request->validate([
            'export' => 'required|in:selected,all',
            'data' => 'array|required_if:export,selected',
            'data.*' => 'uuid|exists:consumable_items,id',
            'search' => 'string|nullable',
            'sort_type' => 'string|nullable|in:asc,desc',
            'sort_quantity' => 'string|nullable|in:asc,desc',
            'sort_major' => 'string|nullable|in:asc,desc',
        ]);

        $user = Auth::user();
        $exportType = $request->input('export');
        $selectedIds = $request->input('data', []);
        $filters = [
            'search' => $request->input('search'),
            'sort_type' => $request->input('sort_type'),
            'sort_quantity' => $request->input('sort_quantity'),
            'sort_major' => $request->input('sort_major'),
        ];

        $filename = 'consumable_items_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new ConsumableItemExport($exportType, $selectedIds, $filters, $user),
            $filename
        );
    }

    public function exportItems(Request $request)
    {
        $request->validate([
            'export' => 'required|in:selected,all',
            'data' => 'array|required_if:export,selected',
            'data.*' => 'uuid|exists:items,id',
            'search' => 'string|nullable',
        ]);

        $exportType = $request->input('export');
        $selectedIds = $request->input('data', []);
        $filters = [
            'search' => $request->input('search'),
        ];

        $filename = 'items_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new ItemExport($exportType, $selectedIds, $filters),
            $filename
        );
    }

    public function exportSubItems(Request $request)
    {
        $request->validate([
            'export' => 'required|in:selected,all',
            'data' => 'array|required_if:export,selected',
            'data.*' => 'uuid|exists:sub_items,id',
            'search' => 'string|nullable',
            'sort_major' => 'string|nullable|in:asc,desc',
            'sort_merk' => 'string|nullable|in:asc,desc',
        ]);

        $exportType = $request->input('export');
        $selectedIds = $request->input('data', []);
        $filters = [
            'search' => $request->input('search'),
            'sort_major' => $request->input('sort_major'),
            'sort_merk' => $request->input('sort_merk'),
        ];

        $filename = 'sub_items_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new SubItemExport($exportType, $selectedIds, $filters),
            $filename
        );
    }
}