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

class SuperadminController extends Controller
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

    public function getMajorLoans(Request $request)
    {
        $fromDate = $request->query('from', now()->startOfYear()->toDateString());
        $toDate   = $request->query('to', now()->endOfYear()->toDateString());

        if ($fromDate > $toDate) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $majors = Major::with([
            'consumableLoans' => function($query) use ($fromDate, $toDate) {
                $query->whereBetween('borrowed_at', [$fromDate, $toDate]);
            },
            'subItems.unitLoans' => function($query) use ($fromDate, $toDate) {
                $query->whereBetween('borrowed_at', [$fromDate, $toDate]);
            }
        ])
        ->get()
        ->map(function($major) {
            $consumableCount = $major->consumableLoans->count();
            $unitCount = $major->subItems->sum(fn($sub) => $sub->unitLoans->count());
            $major->setAttribute('count', $consumableCount + $unitCount);
            return $major;
        });

        return response()->json([
            'status' => 200,
            'data' => CountTotalLoansResource::collection($majors),
        ]);
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
