<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\UnitLoan;
use Carbon\Carbon;
use App\Http\Resources\Admin\ItemsLoansHistoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Admin\ItemCountResource;
use App\Http\Resources\Admin\ItemBorrowPercentage;
use App\Models\SubItem;
use App\Models\UnitItem;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ConsumableItem;
use App\Models\Item;

class AdminUserDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function itemBorrowPercentage(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $data = DB::table('sub_items')
            ->join('items', 'sub_items.item_id', '=', 'items.id')
            ->select('items.name', DB::raw('SUM(sub_items.stock) as total_stock'))
            ->where('sub_items.major_id', Auth::user()->major_id)
            ->when($startDate && $endDate && $startDate === $endDate, function ($query) use ($startDate) {
                return $query->whereDate('borrowed_at', $startDate);
            })
            ->when($startDate && (!$endDate || $startDate !== $endDate), function ($query) use ($startDate) {
                return $query->where('borrowed_at', '>=', $startDate);
            })
            ->when($endDate && (!$startDate || $startDate !== $endDate), function ($query) use ($endDate) {
                return $query->where('borrowed_at', '<=', $endDate);
            })
            ->groupBy('items.name')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => ItemBorrowPercentage::collection($data)
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function index(Request $request)
    {
        // Hitung total semua data
        $totalUnitItems = UnitItem::count();
        $totalConsumables = ConsumableItem::count();
        $total = $totalUnitItems + $totalConsumables;


        // Tampilkan data ke response
        return response()->json([
            'status' => 200,
            'data' => [
                'totalUnitItems' => $totalUnitItems,
                'totalConsumables' => $totalConsumables,
                'total' => $total,
            ]
        ], 200);
    }

    public function getLoanReport(Request $request)
    {
        $fromYear = $request->query('from', Carbon::now()->year);
        $toYear   = $request->query('to', Carbon::now()->year);
        $itemId   = $request->query('item_id', '');

        if (!$fromYear || !$toYear) {
            return response()->json([
                'status' => 400,
                'message' => 'Parameter from dan to wajib diisi'
            ], 400);
        }

        try {
            $startDate = Carbon::create($fromYear, 1, 1)->startOfDay();
            $endDate   = Carbon::create($toYear, 12, 31)->endOfDay();

            $userMajorId = auth()->user()->major_id;

            $loans = UnitLoan::where('status', 1)
                ->whereBetween('borrowed_at', [$startDate, $endDate])
                ->whereHas('unitItem.subItem', function ($q) use ($userMajorId) {
                    $q->where('major_id', $userMajorId);
                })
                ->when($itemId, function ($q) use ($itemId) {
                    $q->whereHas('unitItem.subItem.item', function ($subQ) use ($itemId) {
                        $subQ->where('id', $itemId);
                    });
                })
                ->get();

            // Template bulan
            $months = [
                'Jan' => 0,
                'Feb' => 0,
                'Mar' => 0,
                'Apr' => 0,
                'May' => 0,
                'Jun' => 0,
                'Jul' => 0,
                'Aug' => 0,
                'Sep' => 0,
                'Oct' => 0,
                'Nov' => 0,
                'Dec' => 0,
            ];

            $result = $months;

            foreach ($loans as $loan) {
                $monthEng = Carbon::parse($loan->borrowed_at)->format('M'); // contoh: Jan, Feb, ...
                $result[$monthEng] += 1;
            }

            return response()->json([
                'status' => 200,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function latestActivity(Request $request)
    {
        $user = auth()->user();
        $majorId = $user->major_id;

        $latestLoans = UnitLoan::with('unitItem.subItem')
            ->latestByMajor($majorId, 3)
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $latestLoans
        ]);
    }
}
