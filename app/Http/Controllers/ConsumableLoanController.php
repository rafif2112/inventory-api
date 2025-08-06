<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsumableLoan\StoreValidate;
use App\Http\Requests\ConsumableLoan\UpdateValidate;
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
            'data' => $data,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsumableLoan $consumableLoan)
    {
        $consumableLoanData = $this->consumableLoanService->getConsumableLoanById($consumableLoan);

        return response()->json([
            'status' => 200,
            'data' => $consumableLoanData,
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
                'status' => 204,
                'message' => 'data deleted successfully'
            ], 204);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'failed to delete data'
            ]);
        }
    }
}
