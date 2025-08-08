<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitLoan;
use App\Http\Resources\LogActivityResource;

class LogActivityController extends Controller
{
    public function index() {
        $data = UnitLoan::with(['unitItem', 'student', 'teacher', 'unitItem.subItem', 'unitItem.subItem.item'])->get();

        return response()->json([
            'status' => 200,
            'data' => LogActivityResource::collection($data),
        ], 200);
    }
}
