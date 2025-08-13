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
        $search = request()->query('search', '');

        // Check if there are new students in database
        $latestStudentTimestamp = Student::latest('updated_at')->value('updated_at');
        $cacheKey = 'students_' . md5($search . $latestStudentTimestamp);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($search) {
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
            WHERE
                students.nis::text LIKE CONCAT('%', ?::text, '%')
            ", [$search]);
        });
    }

    public function getStudentData($search = '', $sortMajor = 'asc', $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $cacheKey = 'students_data_' . md5($search . $page . $perPage . $sortMajor);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($search, $perPage, $offset, $page, $sortMajor) {
            $sortOrder = $sortMajor === 'desc' ? 'DESC' : 'ASC';

            $data = DB::select("
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
                students.nis::text LIKE CONCAT('%', ?::text, '%')
            OR
                students.name ILIKE CONCAT('%', ?::text, '%')
            OR
                students.rayon ILIKE CONCAT('%', ?::text, '%')
            ORDER BY 
                majors.name " . $sortOrder . ", students.nis ASC
            LIMIT ? OFFSET ?
        ", [$search, $search, $search, $perPage, $offset]);

            $totalCount = DB::select("
            SELECT COUNT(*) as total
            FROM
                students
            LEFT JOIN
                majors ON students.major_id = majors.id
            WHERE
                students.nis::text LIKE CONCAT('%', ?::text, '%')
            OR
                students.name ILIKE CONCAT('%', ?::text, '%')
            OR
                students.rayon ILIKE CONCAT('%', ?::text, '%')
        ", [$search, $search, $search]);

            $total = $totalCount[0]->total ?? 0;
            $totalPages = ceil($total / $perPage);

            return [
                'data' => $data,
                'meta' => [
                    'current_page' => (int) $page,
                    'from' => (int) (($page - 1) * $perPage + 1),
                    'last_page' => (int) $totalPages,
                    'per_page' => (int) $perPage,
                    'to' => (int) min($page * $perPage, $total),
                    'total' => (int) $total,
                ]
            ];
        });
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
                'rayon' => $data['rayon'],
                'major_id' => $data['major_id'],
            ]);

            $this->clearStudentCache();

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
                'rayon' => $data['rayon'] ?? $student->rayon,
                'major_id' => $data['major_id'] ?? $student->major_id,
            ]);

            $this->clearStudentCache();

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
            $this->clearStudentCache();
            return true;
        } catch (\Throwable $th) {
            Log::error('Failed to delete student: ' . $th->getMessage());
            throw $th;
        }
    }

    private function clearStudentCache()
    {
        $cacheKeys = [
            'students_*',
            'students_data_*'
        ];

        foreach ($cacheKeys as $pattern) {
            Cache::flush();
        }
    }
}
