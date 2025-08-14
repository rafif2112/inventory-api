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
            OR
                students.name ILIKE CONCAT('%', ?::text, '%')
            ", [$search, $search]);
        });
    }

    public function getStudentData($search = '', $sortMajor = 'asc', $page = 1, $perPage = 10)
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $searchParam = trim($search);
        $sortOrder = $sortMajor === 'desc' ? 'DESC' : 'ASC';

        $latestStudentTimestamp = Student::latest('updated_at')->value('updated_at');
        $cacheKey = 'students_data_' . md5($searchParam . $page . $sortMajor . $latestStudentTimestamp);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($searchParam, $perPage, $page, $sortOrder) {

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
        ", [$searchParam, $searchParam, $searchParam]);

            $total = $totalCount[0]->total ?? 0;
            $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

            if ($page > $totalPages && $total > 0) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $perPage;

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
        ", [$searchParam, $searchParam, $searchParam, $perPage, $offset]);

            $hasData = !empty($data);
            $from = $hasData && $total > 0 ? $offset + 1 : 0;
            $to = $hasData ? $offset + count($data) : 0;

            return [
                'data' => $data,
                'meta' => [
                    'current_page' => (int) $page,
                    'from' => (int) $from,
                    'last_page' => (int) $totalPages,
                    'per_page' => (int) $perPage,
                    'to' => (int) $to,
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
