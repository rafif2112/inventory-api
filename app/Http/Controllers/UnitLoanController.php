<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitLoanController extends Controller
{
    // Tampilkan semua unit loan
    public function index()
    {
        $unitLoans = DB::table('unit_loans')->get();
        return response()->json($unitLoans);
    }

    // Simpan unit loan baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'    => 'required|uuid|exists:students,id',
            'teacher_id'    => 'required|uuid|exists:teachers,id',
            'unit_item_id'  => 'required|uuid|exists:unit_items,id',
            'borrowed_by'   => 'required|string',
            'borrowed_at'   => 'required|date',
            'returned_at'   => 'nullable|date',
            'purpose'       => 'required|string',
            'room'          => 'required|integer',
            'status'        => 'boolean',
            'signature'     => 'nullable|string',
            'guarantee'     => 'required|in:BKP,kartu pelajar',
        ]);

        $id = DB::table('unit_loans')->insertGetId($validated);
        $unitLoan = DB::table('unit_loans')->where('id', $id)->first();

        return response()->json($unitLoan, 201);
    }

    // Tampilkan detail unit loan
    public function show($id)
    {
        $unitLoan = DB::table('unit_loans')->where('id', $id)->first();
        return response()->json($unitLoan);
    }

    // Update unit loan
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'student_id'    => 'sometimes|uuid|exists:students,id',
            'teacher_id'    => 'sometimes|uuid|exists:teachers,id',
            'unit_item_id'  => 'sometimes|uuid|exists:unit_items,id',
            'borrowed_by'   => 'sometimes|string',
            'borrowed_at'   => 'sometimes|date',
            'returned_at'   => 'nullable|date',
            'purpose'       => 'sometimes|string',
            'room'          => 'sometimes|integer',
            'status'        => 'boolean',
            'signature'     => 'nullable|string',
            'guarantee'     => 'sometimes|in:BKP,kartu pelajar',
        ]);

        DB::table('unit_loans')->where('id', $id)->update($validated);
        $unitLoan = DB::table('unit_loans')->where('id', $id)->first();

        return response()->json($unitLoan);
    }

    // Hapus unit loan
    public function destroy($id)
    {
        DB::table('unit_loans')->where('id', $id)->delete();

        return response()->json(['message' => 'Unit loan deleted successfully']);
    }
}
