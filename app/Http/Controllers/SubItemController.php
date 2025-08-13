<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SubItem\StoreValidate;
use App\Http\Requests\SubItem\UpdateValidate;
use App\Http\Resources\SubItemResource;
use App\Models\SubItem;
use App\Models\UnitItem;

class SubItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = SubItem::with(['item', 'major'])->get()->map(function ($subItem) {
            $subItem->stock = UnitItem::where('sub_item_id', $subItem->id)->count();
            return $subItem;
        });

        // dd($data);

        return response()->json([
            'status' => 200,
            'data' => SubItemResource::collection($data)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreValidate $request)
    {
        $validated = $request->validated();

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
    public function show(SubItem $subitem)
    {
        $subitem->load(['item', 'major']);
        $subitem->stock = UnitItem::where('sub_item_id', $subitem->id)->count();

        return response()->json([
            'status' => 200,
            'data' => new SubItemResource($subitem),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValidate $request, SubItem $subitem)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $subitem->whereId($subitem->id)->update([
                'item_id' => $validated['item_id'] ?? $subitem->item_id,
                'merk' => $validated['merk'] ?? $subitem->merk,
                'stock' => $validated['stock'] ?? $subitem->stock,
                'unit' => $validated['unit'] ?? $subitem->unit,
                'major_id' => $validated['major_id'] ?? $subitem->major_id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Sub item updated successfully',
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
    public function destroy(SubItem $subitem)
    {
        DB::beginTransaction();

        try {
            if(!$subitem) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Sub item not found',
                ], 404);
            }

            $subitem->whereId($subitem->id)->delete();

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
