<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsumableItem\StoreValidate;
use App\Http\Requests\ConsumableItem\UpdateValidate;
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
    public function index()
    {
        $data = $this->consumableItemService->getAllConsumableItems();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
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
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsumableItem $consumableItem)
    {
        $consumableItemData = $this->consumableItemService->getConsumableItemById($consumableItem);

        return response()->json([
            'status' => 'success',
            'data' => $consumableItemData,
        ]);
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
                'status' => 'error',
                'message' => 'failed to update data'
            ]);
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
                    'status' => 'error',
                    'message' => 'data not found'
                ], 404);
            }

            $this->consumableItemService->deleteConsumableItem($consumableItem);

            return response()->json([
                'status' => 'success',
                'message' => 'data deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'failed to delete data'
            ]);
        }
    }
}
