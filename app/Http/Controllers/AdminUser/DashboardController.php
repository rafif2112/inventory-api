<?php

namespace App\Http\Controllers\AdminUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Admin\ItemCountResource;
use App\Http\Resources\Admin\ItemBorrowPercentage;
use App\Models\SubItem;
use App\Models\UnitItem;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    public function itemCount()
    {
        $data = SubItem::where('major_id', Auth::user()->major_id)
        ->with(['item'])
        ->get()
        ->map(function ($subItem) {
            $subItem->setAttribute('stock', UnitItem::where('sub_item_id', $subItem->id)->count());
            return $subItem;
        });

        return response()->json([
            'status' => 200,
            'data' => ItemcountResource::collection($data)
        ], 200);
    }

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
