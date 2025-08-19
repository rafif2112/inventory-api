<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitLoan;
use App\Http\Resources\LogActivityResource;

class LogActivityController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $data = UnitLoan::with(['unitItem', 'student', 'teacher', 'unitItem.subItem', 'unitItem.subItem.item'])
        ->when($startDate && $endDate && $startDate === $endDate, function ($query) use ($startDate) {
            return $query->whereDate('borrowed_at', $startDate);
        })
        ->when($startDate && (!$endDate || $startDate !== $endDate), function ($query) use ($startDate) {
            return $query->where('borrowed_at', '>=', $startDate);
        })
        ->when($endDate && (!$startDate || $startDate !== $endDate), function ($query) use ($endDate) {
            return $query->where('borrowed_at', '<=', $endDate);
        })
        ->get();

        return response()->json([
            'status' => 200,
            'data' => LogActivityResource::collection($data),
        ], 200);
    }
}
