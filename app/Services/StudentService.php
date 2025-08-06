<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Log;

class StudentService
{
    /**
     * Get all students with their major
     */
    public function getAllStudents()
    {
        return Student::with('major')->get();
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
                'rombel' => $data['rombel'],
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
        $student->load('major');
        return $student;
    }

    /**
     * Update student data
     */
    public function updateStudent(Student $student, array $data)
    {
        try {
            $student->whereId($student->id)->update([
                'name' => $data['name'],
                'nis' => $data['nis'],
                'rombel' => $data['rombel'],
                'rayon' => $data['rayon'],
                'major_id' => $data['major_id'],
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
