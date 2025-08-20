<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsumableLoan\StoreValidate;
use App\Http\Requests\ConsumableLoan\UpdateValidate;
use App\Http\Resources\ConsumableLoanResource;
use App\Http\Resources\PaginationResource;
use App\Models\ConsumableLoan;
use App\Services\ConsumableLoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsumableLoanController extends Controller
{
    protected $consumableLoanService;

    public function __construct(ConsumableLoanService $consumableLoanService)
    {
        $this->consumableLoanService = $consumableLoanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->consumableLoanService->getAllConsumableLoans();

        return response()->json([
            'status' => 200,
            'data' => ConsumableLoanResource::collection($data),
        ], 200);
    }

    public function getConsumableLoanHistory(Request $request)
    {
        try {
            $sortQuantity = $request->query('sort_quantity', 'asc');
            $sortType = $request->query('sort_type', 'asc');
            $search = $request->query('search', '');

            $data = $this->consumableLoanService->getConsumableLoanHistory($search, $sortQuantity, $sortType);

            return response()->json([
                'status' => 200,
                'data' => ConsumableLoanResource::collection($data),
                'meta' => new PaginationResource($data)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsumableLoan $consumableLoan)
    {
        $consumableLoanData = $this->consumableLoanService->getConsumableLoanById($consumableLoan);

        return response()->json([
            'status' => 200,
            'data' => ConsumableLoanResource::make($consumableLoanData),
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
            $newData = $this->consumableLoanService->createConsumableLoan($data);

            DB::commit();
            return response()->json([
                'status' => 201,
                'message' => 'Consumable loan created successfully'
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
     * Update the specified resource in storage.
     */
    public function update(UpdateValidate $request, ConsumableLoan $consumableLoan)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $updatedConsumableLoan = $this->consumableLoanService->updateConsumableLoan($consumableLoan, $data);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Consumable loan updated successfully'
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
    public function destroy(ConsumableLoan $consumableLoan)
    {
        try {
            if (!$consumableLoan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'data not found'
                ], 404);
            }

            $this->consumableLoanService->deleteConsumableLoan($consumableLoan);

            return response()->json([
                'status' => 200,
                'message' => 'data deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'failed to delete data'
            ]);
        }
    }
}
