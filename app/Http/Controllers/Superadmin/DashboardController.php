<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\UnitItem;
use App\Models\ConsumableItem;

class DashboardController extends Controller
{
    /**
     * API Dashboard utama
     */
    public function index()
    {
        $totalTeachers    = Teacher::count();
        $totalStudents    = Student::count();
        $totalUnitItems   = UnitItem::count();
        $totalConsumables = ConsumableItem::count();
        $latestUnitItems  = UnitItem::latest()->take(5)->get();

        return response()->json([
            'totalTeachers'    => $totalTeachers,
            'totalStudents'    => $totalStudents,
            'totalUnitItems'   => $totalUnitItems,
            'totalConsumables' => $totalConsumables,
            'latestUnitItems'  => $latestUnitItems
        ]);
    }

    public function student()
    {
        $totalStudents = Student::count();

        return response()->json([
            'totalStudents' => $totalStudents,
        ]);
    }

    public function teacher()
    {
        $totalTeachers = Teacher::count();

        return response()->json([
            'totalTeachers' => $totalTeachers,
        ]);
    }

    public function unitItem()
    {
        $totalUnitItems  = UnitItem::count();
        $latestUnitItems = UnitItem::latest()->take(5)->get();

        return response()->json([
            'totalUnitItems'  => $totalUnitItems,
            'latestUnitItems' => $latestUnitItems,
        ]);
    }

    public function consumable()
    {
        $totalConsumables = ConsumableItem::count();

        return response()->json([
            'totalConsumables' => $totalConsumables,
        ]);
    }
}
//     }
