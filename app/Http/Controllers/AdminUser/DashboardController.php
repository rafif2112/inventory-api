<?php

namespace App\Http\Controllers\AdminUser;

use App\Http\Controllers\Controller;
use App\Http\Resources\Superadmin\ItemsLoansHistoryResource;
use App\Models\UnitItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function getItemsLoansHistory()
    {
        $items = UnitItem::with(['subItem' => function($q) {
                        $q->latest()->limit(1); 
                    }])
                    ->latest()
                    ->take(5)
                    ->get();

        return response()->json([
            'status' => 200,
            'data' => ItemsLoansHistoryResource::collection($items)
        ]);
    }

    public function index()
    {
        //
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
