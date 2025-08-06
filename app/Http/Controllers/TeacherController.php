<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Services\TeacherService;
use Illuminate\Http\Request;

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
            'status' => 'success',
            'data' => $data,
        ]);
    }

    // public function index(Request $request)
    // {
    //     $search = $request->search;
    //     $data = $this->teacherService->searchTeachers($search);

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $data,
    //     ]);
    // }

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
            'status' => 'success',
            'data' => $teacherData,
        ]);
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
}
