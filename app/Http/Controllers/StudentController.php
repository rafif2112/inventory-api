<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\StoreValidate;
use App\Http\Requests\Student\UpdateValidate;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\StudentResource;
use App\Imports\StudentImport;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->studentService->getAllStudents();

        return response()->json([
            'status' => 200,
            'data' => StudentResource::collection($data),
        ], 200);
    }

    public function getStudentData(Request $request)
    {
        $search = $request->query('search', '');
        $sortMajor = $request->query('sort_major');
        $page = $request->query('page', 1);
        $data = $this->studentService->getStudentData($search, $sortMajor, $page);

        return response()->json([
            'status' => 200,
            'data' => StudentResource::collection($data['data']),
            'meta' => new PaginationResource($data['meta']),
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
            $newData = $this->studentService->createStudent($data);

            DB::commit();
            return response()->json([
                'status' => 201,
                'data' => $newData,
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'failed to create new data'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $studentData = $this->studentService->getStudentById($student);

        return response()->json([
            'status' => 200,
            'data' => new StudentResource($studentData),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValidate $request, Student $student)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $updatedStudent = $this->studentService->updateStudent($student, $data);

            DB::commit();
            return response()->json([
                'status' => 200,
                'data' => $updatedStudent,
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
    public function destroy(Student $student)
    {
        DB::beginTransaction();
        try {
            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'data not found'
                ], 404);
            }

            $this->studentService->deleteStudent($student);

            DB::commit();
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

    public function resetData()
    {
        DB::beginTransaction();
        try {
            $this->studentService->resetData();

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Student data has been reset successfully.'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reset student data: ' . $th->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ],[
            'file.required' => 'File is required',
            'file.file' => 'The uploaded file must be a valid file',
            'file.mimes' => 'The file must be a file of type: xlsx, csv, xls',
        ]);
        $file = $request->file('file');

        if (!$file) {
            return response()->json([
                'status' => 'error',
                'message' => 'No file uploaded'
            ], 400);
        }

        try {
            $import = new StudentImport();
            Excel::import($import, $file);

            // Excel::import(new StudentImport, $request->file('file'));

            return response()->json([
                'status' => 201,
                'message' => 'Data imported successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to import data: ' . $e->getMessage()
            ], 500);
        }
    }
}
