<?php

namespace App\Http\Controllers;

use App\Models\SubItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = SubItem::select('*')
            ->latest()  
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'merk' => 'required|string|max:255',
            'stock' => 'required|numeric',
            'unit' => 'required|string|max:50',
            'major_id' => 'required|exists:majors,id',
        ]);

        DB::beginTransaction();
        try {
            
            $subItem = SubItem::create($validated);

            DB::commit();
            return response()->json([
                'status' => 201,
                'message' => 'Sub item created successfully',
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create sub item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubItem $subItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubItem $subItem)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'merk' => 'required|string|max:255',
            'stock' => 'required|numeric',
            'unit' => 'required|string|max:50',
            'major_id' => 'required|exists:majors,id',
        ]);

        DB::beginTransaction();
        try {
            $subItem->update($validated);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Sub item updated successfully',
                'data' => $subItem,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update sub item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubItem $subItem)
    {
        DB::beginTransaction();

        try {
            if(!$subItem) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Sub item not found',
                ], 404);
            }

            $subItem->whereId($subItem->id)->delete();

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Sub item deleted successfully',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete sub item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
