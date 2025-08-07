<?php

namespace App\Http\Controllers;

use App\Imports\TeacherImport;
use App\Models\Teacher;
use App\Services\TeacherService;
use Illuminate\Http\Request;
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

        if ($search) {
            $data = $this->teacherService->searchTeachers($search);
        } else {
            $data = $this->teacherService->getAllTeachers();
        }

        return response()->json([
            'status' => 200,
            'data' => $data,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $teacherData = $this->teacherService->getTeacherById($id);

        return response()->json([
            'status' => 200,
            'data' => $teacherData,
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
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
            $import = new TeacherImport();
            Excel::import($import, $file);

            return response()->json([
                'status' => 'success',
                'message' => 'Data imported successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to import data: ' . $e->getMessage()
            ], 500);
        }
    }
}
