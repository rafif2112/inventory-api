<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\UnitLoan;
use App\Models\Major;
use App\Models\ConsumableLoan;


use App\Http\Controllers\Controller;
use App\Http\Resources\Superadmin\CountTotalLoansResource;
use App\Models\Item;
use App\Models\UnitItem;
use Illuminate\Http\Request;
use App\Services\Superadmin\DashboardService;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ConsumableItem;

class SuperadminDashboardController extends Controller
{
    /**
     * API Dashboard utama
     */
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    // public function getItemsLoansHistory()
    // {
    //     $items = UnitItem::with(['subItems', 'unitItems'])->get();

    //     return ItemsLoansHistoryResource::collection($items);
    // }

    public function getMajorLoans()
    {
        $majors = Major::with(['consumableLoans', 'subItems.unitLoans'])->get();

        return CountTotalLoansResource::collection($majors);
    }

    public function index()
    {
        $totalTeachers    = Teacher::count();
        $totalStudents    = Student::count();
        $totalUnitItems   = UnitItem::count();
        $totalConsumables = ConsumableItem::count();

        return response()->json([
            'status' => 200,
            'data' => [
                'totalTeachers'    => $totalTeachers,
                'totalStudents'    => $totalStudents,
                'totalUnitItems'   => $totalUnitItems,
                'totalConsumables' => $totalConsumables,
            ]
        ]);
    }

    public function latestUnitItems()
    {
        $latestUnitItems = UnitItem::latest()->take(5)->get();

        return response()->json([
            'latestUnitItems' => $latestUnitItems,
        ]);
    }


    // Most of Borrowing
    public function indexBorrowing()
    {
        $data = $this->dashboardService->getItemsSummary();

        return response()->json([
            'status' => 200,
            'data' => $data,
        ], 200);
    }

    public function indexAverageBorrowing()
    {
        $data = $this->dashboardService->getAverageBorrowing();

        return response()->json([
            'status' => 200,
            'data' => $data,
        ], 200);
    }
}
