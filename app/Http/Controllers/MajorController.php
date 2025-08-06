<?php

namespace App\Http\Controllers;

use App\Http\Requests\Major\StoreValidate;
use App\Http\Requests\Major\UpdateValidate;
use App\Models\Major;
use App\Services\MajorService;
use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MajorController extends Controller
{

    protected $majorService;

    public function __construct(MajorService $majorService)
    {
        $this->majorService = $majorService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = $this->majorService->getAllMajors();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No majors found',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $data,
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreValidate $request)
    {
        //
        $data = $request->validated();

        DB::beginTransaction();
        try {

            $this->majorService->storeMajor($request, $data);

            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Major created successfully',
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
            'status' => 500,
            'message' => 'Failed to create major',
            'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Major $major)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValidate $request, Major $major)
    {
        //
        $validated = $request->validated();

        DB::beginTransaction();
        try {

            $this->majorService->updateMajor($major, $validated, $request);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Major updated successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update major',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Major $major)
    {
        //
        DB::beginTransaction();
        try {

            $this->majorService->deleteMajor($major);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Major deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete major',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
