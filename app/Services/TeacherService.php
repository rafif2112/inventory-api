<?php
namespace App\Services;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeacherService
{
    public function getAllTeachers()
    {
        // return Teacher::with('name')->get();
        return Teacher::all();
    }

    public function getTeacherById($id)
    {
        return Teacher::find($id);
    }

     public function searchTeachers($search = null)
    {
        return Teacher::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->latest()
            ->get();
    }

//    public function searchTeachers(Request $request)
// {
//     $teacher = Teacher::query()
//         ->when($request->search, function ($query, $search) {
//             $query->where('name', 'like', "%$search%");
//         })
//         ->latest()
//         ->get();

//     // return response()->json($teacher);
// }


}