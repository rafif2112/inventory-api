<?php

namespace App\Http\Controllers;

// use App\Http\Requests\ConsumableLoan\StoreValidate;
// use App\Http\Requests\ConsumableLoan\UpdateValidate;
use App\Models\ConsumableLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Str;

class ConsumableLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ConsumableLoan::select('*')
        ->latest()
        ->get();

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'student_id' => 'nullable|exists:students,id',
        //     'teacher_id' => 'nullable|exists:teachers,id',
        //     'consumable_item_id' => 'nullable|exists:consumable_items,id',
        //     'quantity' => 'required|integer|min:1',
        //     'purpose' => 'nullable|string|max:255',
        //     'borrowed_by' => 'required|string|max:100',
        //     'borrowed_at' => 'nullable|date',
        // ]);


        DB::beginTransaction();
        try {
            // $ConsumableLoan = ConsumableLoan::create([
            //     'student_id' => $request->student_id,
            //     'teacher_id' => $request->teacher_id,
            //     'consumable_item_id' => $request->consumable_item_id,
            //     'quantity' => $request->quantity,
            //     'purpose' => $request->purpose,
            //     'borrowed_by' => $request->borrowed_by,
            //     'borrowed_at' => $request->borrowed_at,
            // ]);

            $ConsumableLoan = ConsumableLoan::create($request->all());
            
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'data created successfully'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'failed to create data',
                'error' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsumableLoan $consumableLoan)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConsumableLoan $consumableLoan)
    {
        // $validatedData = $request->validate([
        //     // 
        // ])

        DB::beginTransaction();
        try {
            $ConsumableLoan->whereId($ConsumableLoan->id)->update($request->all());

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'data updated successfully'
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
        DB::beginTransaction();
        try {
            if (!$ConsumableLoan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'data not found'
                ], 404);
            }

            $ConsumableLoan->whereId($ConsumableLoan->id)->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
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
