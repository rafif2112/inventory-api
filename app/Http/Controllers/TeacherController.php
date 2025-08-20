<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaginationResource;
use App\Http\Resources\TeacherResource;
use App\Imports\TeacherImport;
use App\Models\Teacher;
use App\Services\TeacherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    protected $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    public function index(Request $request)
    {
        $search = $request->search;

        $data = $this->teacherService->getAllTeachers($search);

        return response()->json([
            'status' => 200,
            'data' => TeacherResource::collection($data),
        ], 200);
    }

    public function getTeachersData(Request $request)
    {
        $search = $request->query('search', '');
        $page = $request->query('page', 1);

        $teachersData = $this->teacherService->getTeachersData($search, $page);

        return response()->json([
            'status' => 200,
            'data' => TeacherResource::collection($teachersData['data']),
            'meta' => new PaginationResource($teachersData['meta']),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nip' => 'required|unique:teachers,nip',
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
        ]);

        $teacher = $this->teacherService->createTeacher($validatedData);

        return response()->json([
            'status' => 201,
            'message' => 'Teacher created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $teacherData = $this->teacherService->getTeacherById($id);

        if (!$teacherData) {
            return response()->json([
                'status' => 404,
                'message' => 'Teacher not found',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => new TeacherResource($teacherData),
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $validatedData = $request->validate([
            'nip' => 'required|unique:teachers,nip,' . $teacher->id,
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
        ]);

        $updatedTeacher = $this->teacherService->updateTeacher($teacher, $validatedData);

        return response()->json([
            'status' => 200,
            'message' => 'Teacher updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        $deleted = $this->teacherService->deleteTeacher($teacher);

        return response()->json([
            'status' => 200,
            'message' => 'Teacher deleted successfully',
        ], 200);
    }

    public function resetData()
    {
        DB::beginTransaction();
        try {
            $this->teacherService->resetTeachersData();

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Teacher data reset successfully',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to reset teacher data: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ], [
            'file.required' => 'File is required',
            'file.file' => 'The uploaded file must be a valid file',
            'file.mimes' => 'The file must be a file of type: xlsx, csv, xls',
        ]);
        $file = $request->file('file');

        if (!$file) {
            return response()->json([
                'status' => 400,
                'message' => 'No file uploaded'
            ], 400);
        }

        try {
            $import = new TeacherImport();
            Excel::import($import, $file);

            return response()->json([
                'status' => 200,
                'message' => 'Data imported successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to import data: ' . $e->getMessage()
            ], 500);
        }
    }
}
