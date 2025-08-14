<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitLoan\StoreValidate;
use App\Http\Requests\UnitLoan\UpdateValidate;
use App\Http\Resources\CheckLoan\IsBorrowedResource;
use App\Http\Resources\UnitItemResource;
use App\Http\Resources\UnitLoanResource;
use App\Models\UnitLoan;
use App\Services\UnitLoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitLoanController extends Controller
{
    protected $unitLoanService;

    public function __construct(UnitLoanService $unitLoanService)
    {
        $this->unitLoanService = $unitLoanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unitLoans = $this->unitLoanService->getAllUnitLoans();

        return response()->json([
            'status' => 200,
            // 'data' => $unitLoans
            'data' => UnitLoanResource::collection($unitLoans)
        ], 200);
    }

    /**
     * Get loan status by unit code
     */
    public function getLoan(Request $request)
    {
        try {
            $request->validate([
                'code_unit' => 'required|string|exists:unit_items,code_unit',
            ]);

            $result = $this->unitLoanService->getLoanByUnitCode($request->code_unit);

            if (!$result['found']) {
                return response()->json([
                    'status' => 404,
                    'message' => $result['message']
                ], 404);
            }

            if (!$result['is_borrowed']) {
                return response()->json([
                    'status' => 200,
                    'data' => new UnitItemResource($result['data']),
                ], 200);
            }

            return response()->json([
                'status' => 200,
                'data' => new IsBorrowedResource($result['data'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to get loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreValidate $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            
            $unitLoan = $this->unitLoanService->createUnitLoan($validated, $request);

            $unitLoan->load(['student', 'teacher', 'unitItem']);

            DB::commit();
            return response()->json([
                'status' => 200,
                'data' => new UnitLoanResource($unitLoan),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $unitLoan = $this->unitLoanService->getUnitLoanById($id);

        if (!$unitLoan) {
            return response()->json([
                'status' => 404,
                'message' => 'Unit loan not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $unitLoan
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValidate $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            
            $unitLoan = UnitLoan::find($id);
            if (!$unitLoan) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Unit loan not found'
                ], 404);
            }

            $this->unitLoanService->updateUnitLoan($unitLoan, $validated, $request);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Unit loan updated successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update unit loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $unitLoan = UnitLoan::find($id);
            if (!$unitLoan) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Unit loan not found'
                ], 404);
            }

            $this->unitLoanService->deleteUnitLoan($unitLoan);

            return response()->json([
                'status' => 200,
                'message' => 'Unit loan deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete unit loan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLoanHistory(Request $request)
    {
        try {
            $sortTime = $request->query('sort_by_time', 'desc');
            $sortType = $request->query('sort_by_type');
            $search = strtolower($request->query('search'));
            $data = $request->query('data');

            $history = $this->unitLoanService->getLoanHistory($sortTime, $sortType, $search, $data);

            return response()->json([
                'status' => 200,
                'data' => UnitLoanResource::collection($history)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to retrieve loan history: ' . $th->getMessage()
            ], 500);
        }
    }
}