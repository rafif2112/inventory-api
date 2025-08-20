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

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getMajorLoans()
    {
        $majors = Major::with(['consumableLoans', 'subItems.unitLoans'])->get();

        return CountTotalLoansResource::collection($majors);
    }


    // public function getItemsLoansHistory()
    // {
    //     $items = UnitItem::with(['subItems', 'unitItems'])->get();

    //     return ItemsLoansHistoryResource::collection($items);
    // }

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
