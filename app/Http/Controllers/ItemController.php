<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = Item::select('*')
            ->Latest()
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
        //
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {

            $item = Item::create([
                'name' => $request->name,
            ]);

            DB::commit();
            return response()->json([
                'status' => 201,
                'message' => 'Item created successfully',
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create item',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
        return response()->json([
            'status' => 200,
            'data' => $item,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        //
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $item->whereId($item->id)->update($validatedData);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Item updated successfully',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update item',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        //
        DB::beginTransaction();
        try {
            if (!$item) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Item not found',
                ], 404);
            }

            $item->whereId($item->id)->delete();

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Item deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete item',
            ], 500);
        }
    }
}
