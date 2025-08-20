<?php

namespace App\Http\Controllers\Superadmin;

use App\Models\UnitLoan;
use App\Models\Major;
use App\Models\ConsumableLoan;


use App\Http\Controllers\Controller;
use App\Http\Resources\Superadmin\CountTotalLoansResource;
use App\Http\Resources\Superadmin\ItemsLoansHistoryResource;
use App\Models\Item;
use App\Models\UnitItem;
use Illuminate\Http\Request;
use App\Services\Superadmin\DashboardService;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }


    public function index()
    {
        $majors = Major::with(['consumableLoans', 'subItems.unitLoans'])->get();

        return CountTotalLoansResource::collection($majors);
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
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
