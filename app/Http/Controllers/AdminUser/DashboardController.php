<?php

namespace App\Http\Controllers\AdminUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\UnitItem;
use App\Models\ConsumableItem;
use App\Models\Item;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Hitung total semua data
        $totalUnitItems = UnitItem::count();
        $totalConsumables = ConsumableItem::count();
        $total = $totalUnitItems + $totalConsumables;


        // Tampilkan data ke view
        return view('admin.dashboard', [
            'totalUnitItems' => $totalUnitItems,
            'total' => $total,
            'totalConsumables' => $totalConsumables,


        ]);
    }

    public function unitItem()
    {
        $totalUnitItems = UnitItem::count();
        $latestUnitItems = UnitItem::latest()->take(5)->get();

        return response()->json([
            'totalUnitItems' => $totalUnitItems,
            'latestUnitItems' => $latestUnitItems,
        ]);
    }

    public function consumableItem()
    {
        $totalConsumables = ConsumableItem::count();
        $latestConsumables = ConsumableItem::latest()->take(5)->get();

        return response()->json([
            'totalConsumables' => $totalConsumables,
            'latestConsumables' => $latestConsumables,
        ]);
    }

    public function item()
    {
        $totalItems = Item::count();
        $latestItems = Item::latest()->take(5)->get();

        return response()->json([
            'totalItems' => $totalItems,
            'latestItems' => $latestItems,
        ]);
    }

}
