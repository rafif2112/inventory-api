<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitItem\StoreValidate;
use App\Http\Requests\UnitItem\UpdateValidate;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\UnitItemResource;
use App\Models\UnitItem;
use App\Services\UnitItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitItemController extends Controller
{
    protected $unitItemService;

    public function __construct(UnitItemService $unitItemService)
    {
        $this->unitItemService = $unitItemService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
        $sortDate = $request->query('sort_date');
        $sortType = $request->query('sort_type');
        $sortCondition = $request->query('sort_condition');
        $sortMajor = $request->query('sort_major');

        $unitItems = $this->unitItemService->getAllUnitItems($user, $search, $sortDate, $sortType, $sortCondition, $sortMajor);

        return response()->json([
            'status' => 200,
            'data' => UnitItemResource::collection($unitItems->items()),
            'meta' => new PaginationResource($unitItems)
        ], 200);
    }

    public function listUnitItem(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');

        $unitItems = $this->unitItemService->getListUnitItem($user, $search);

        return response()->json([
            'status' => 200,
            'data' => UnitItemResource::collection($unitItems),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreValidate $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $newUnitItem = $this->unitItemService->storeUnitItem($data);
            DB::commit();
            return response()->json([
                'status' => 201,
                'message' => 'Unit item created successfully',
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create unit item: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UnitItem $unitItem)
    {
        $unitItem->load('subItem', 'subItem.item', 'subItem.major');
        return response()->json([
            'status' => 200,
            'data' => new UnitItemResource($unitItem),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValidate $request, UnitItem $unitItem)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $updatedUnitItem = $this->unitItemService->updateUnitItem($unitItem, $data);
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Unit item updated successfully',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update unit item: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnitItem $unitItem)
    {
        DB::beginTransaction();
        try {
            $subItem = $unitItem->subItem;

            if ($subItem) {
                $subItem->decrement('stock');
            }

            $unitItem->whereId($unitItem->id)->delete();

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Unit item deleted successfully',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete unit item: ' . $e->getMessage(),
            ], 500);
        }
    }
}
