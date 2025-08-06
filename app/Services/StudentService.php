<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentService
{
    /**
     * Get all students with their major
     */
    public function getAllStudents()
    {
        // return Student::with('major')->get();
        return Cache::remember('students_with_major', 60, function () {
            return DB::select("
            SELECT 
                students.*,
                majors.id AS major_id,
                majors.name AS major_name,
                majors.icon,
                majors.color
            FROM 
                students
            LEFT JOIN 
                majors ON students.major_id = majors.id
            ");
        });

        // $students = Student::where('name', 'LIKE', '%' . 'Muhamad Rafif' . '%')->get();
        // return $students;
    }

    /**
     * Create a new student
     */
    public function createStudent(array $data)
    {
        try {
            $newStudent = Student::create([
                'name' => $data['name'],
                'nis' => $data['nis'],
                // 'rombel' => $data['rombel'],
                'rayon' => $data['rayon'],
                'major_id' => $data['major_id'],
            ]);

            return $newStudent;
        } catch (\Throwable $th) {
            Log::error('Failed to create student: ' . $th->getMessage());
            throw $th;
        }
    }

    /**
     * Get student by id with major
     */
    public function getStudentById(Student $student)
    {
        $studentData = DB::select("
            SELECT 
                students.*, 
                majors.id AS major_id,
                majors.name AS major_name,
                majors.icon,
                majors.color
            FROM 
                students
            LEFT JOIN 
                majors ON students.major_id = majors.id
            WHERE 
                students.id = ?
        ", [$student->id]);

        return $studentData[0] ?? null;
    }

    /**
     * Update student data
     */
    public function updateStudent(Student $student, array $data)
    {
        try {
            $student->update([
                'name' => $data['name'] ?? $student->name,
                'nis' => $data['nis'] ?? $student->nis,
                // 'rombel' => $data['rombel'] ?? $student->rombel,
                'rayon' => $data['rayon'] ?? $student->rayon,
                'major_id' => $data['major_id'] ?? $student->major_id,
            ]);

            return $student;
        } catch (\Throwable $th) {
            Log::error('Failed to update student: ' . $th->getMessage());
            throw $th;
        }
    }

    /**
     * Delete student
     */
    public function deleteStudent(Student $student)
    {
        try {
            $student->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error('Failed to delete student: ' . $th->getMessage());
            throw $th;
        }
    }
}
