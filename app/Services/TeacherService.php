<?php
namespace App\Services;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherService
{
    public function getAllTeachers($search = '')
    {
        // return Teacher::all();

        // $searchTerm = '%' . (string)($search ?? '') . '%';
        $teachers = DB::select("
            SELECT * FROM teachers
            WHERE 
                teachers.nip::text LIKE CONCAT('%', ?::text, '%')
            OR 
                teachers.name LIKE CONCAT('%', ?::text, '%')
            ORDER BY teachers.name ASC
        ", [$search, $search]);

        return $teachers;
    }

    public function getTeacherById($id)
    {
        return Teacher::find($id);
    }
}