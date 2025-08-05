<?php

namespace App\Http\Controllers;

use App\Http\Requests\student\StoreValidate;
use App\Http\Requests\student\UpdateValidate;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'status' => 'success',
            'data' => $data,
        ]);
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
            'status' => 'success',
            'data' => $studentData,
        ]);
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
                'status' => 'success',
                'data' => $updatedStudent,
            ]);
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
        try {
            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'data not found'
                ], 404);
            }

            $this->studentService->deleteStudent($student);

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
