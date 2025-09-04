<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\AdminUser\MobileDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileController extends Controller
{
    protected $mobileDashboardService;

    public function __construct(MobileDashboardService $mobileDashboardService)
    {
        $this->mobileDashboardService = $mobileDashboardService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Implement your logic to retrieve the index data
    }

    public function getCardData(Request $request)
    {
        try {
            $user = Auth::user();

            $data = $this->mobileDashboardService->getCardData($user);

            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function latestActivity(Request $request)
    {
        try {
            $user = Auth::user();

            $data = $this->mobileDashboardService->getLatestActivity($user);

            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
