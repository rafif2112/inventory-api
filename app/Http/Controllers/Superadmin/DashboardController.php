<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Superadmin\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }


    public function index()
    {
        //
    }

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
