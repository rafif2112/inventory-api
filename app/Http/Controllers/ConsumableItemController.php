<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsumableItem\StoreValidate;
use App\Http\Requests\ConsumableItem\UpdateValidate;
use App\Http\Resources\ConsumableItemResource;
use App\Http\Resources\PaginationResource;
use App\Models\ConsumableItem;
use App\Services\ConsumableItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsumableItemController extends Controller
{
    protected $consumableItemService;

    public function __construct(ConsumableItemService $consumableItemService)
    {
        $this->consumableItemService = $consumableItemService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $sortType = $request->query('sort_type', '');
        $sortQuantity = $request->query('sort_quantity', '');
        $sortMajor = $request->query('sort_major', '');

        $data = $this->consumableItemService->getAllConsumableItems(
            auth()->user(),
            $search,
            $sortType,
            $sortQuantity,
            $sortMajor
        );

        return response()->json([
            'status' => 200,
            'data' => ConsumableItemResource::collection($data),
        ], 200);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $sortType = $request->query('sort_type', '');
        $sortQuantity = $request->query('sort_quantity', '');
        $sortMajor = $request->query('sort_major', '');

        $data = $this->consumableItemService->getDataConsumableItems(
            auth()->user(),
            $search,
            $sortType,
            $sortQuantity,
            $sortMajor,
        );

        return response()->json([
            'status' => 200,
            'data' => ConsumableItemResource::collection($data),
            'meta' => new PaginationResource($data)
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreValidate $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $newData = $this->consumableItemService->createConsumableItem($data);

            DB::commit();
            return response()->json([
                'status' => 201,
                'message' => 'Consumable item created successfully',
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsumableItem $consumableItem)
    {
        $consumableItemData = $this->consumableItemService->getConsumableItemById($consumableItem);

        return response()->json([
            'status' => 200,
            'data' => $consumableItemData,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConsumableItem $consumableItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValidate $request, ConsumableItem $consumableItem)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $updatedConsumableItem = $this->consumableItemService->updateConsumableItem($consumableItem, $data);

            DB::commit();
            return response()->json([
                'status' => 200,
                'massage' => 'Consumable item updated successfully',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'failed to update data'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsumableItem $consumableItem)
    {
        try {
            if (!$consumableItem) {
                return response()->json([
                    'status' => 404,
                    'message' => 'data not found'
                ], 404);
            }

            $this->consumableItemService->deleteConsumableItem($consumableItem);

            return response()->json([
                'status' => 200,
                'message' => 'data deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'failed to delete data'
            ], 500);
        }
    }
}
