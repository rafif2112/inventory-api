<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Http\Resources\PaginationResource;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    protected $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search', '');
        $data = $this->itemService->getAllItems($search);

        return response()->json([
            'status' => 200,
            'data' => $data,
        ], 200);
    }

    public function itemPaginate(Request $request)
    {
        $search = request()->query('search', '');

        $data = $this->itemService->getItemPaginate($search);

        return response()->json([
            'status' => 200,
            'data' => ItemResource::collection($data->items()),
            'meta' => new PaginationResource($data)
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

            $lastItem = Item::orderBy('id', 'desc')->first();
            $codeItem = $lastItem ? $lastItem->code_item + 1 : 1;

            $item = Item::create([
                'name' => $request->name,
                'code_item' => $codeItem,
            ]);

            DB::commit();
            return response()->json([
                'status' => 201,
                'message' => 'Item created successfully',
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
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
                'status' => 500,
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
