<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentService
{
    public function getAllStudents()
    {
        $search = trim(request()->query('search', ''));

        $studentsTimestamp = Student::max('updated_at') ?? now();
        $dataVersion = md5($studentsTimestamp);

        $allDataCacheKey = 'students_all_' . $dataVersion;

        $allStudents = Cache::remember($allDataCacheKey, now()->addHours(1), function () {
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
                ORDER BY students.name ASC
            ");
        });

        if (empty($search)) {
            return $allStudents;
        }

        $searchLower = strtolower($search);
        $filteredResults = array_values(array_filter($allStudents, function ($student) use ($searchLower) {
            return str_contains(strtolower($student->nis ?? ''), $searchLower) ||
                str_contains(strtolower($student->name ?? ''), $searchLower) ||
                str_contains(strtolower($student->rayon ?? ''), $searchLower);
        }));

        return $filteredResults;
    }

    public function getStudentData($search = '', $sortMajor = 'asc', $page = 1, $perPage = 10)
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $searchParam = trim($search);
        $sortOrder = $sortMajor === 'desc' ? 'DESC' : 'ASC';

        $latestStudentTimestamp = Student::latest('updated_at')->value('updated_at');
        $dataVersion = md5($latestStudentTimestamp);

        $allDataCacheKey = 'students_all_data_' . $dataVersion . '_' . md5($sortMajor);

        $allStudents = Cache::remember($allDataCacheKey, now()->addHours(1), function () use ($sortOrder) {
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
            ORDER BY 
                majors.name " . $sortOrder . ", students.nis ASC
        ");
        });

        if (empty($searchParam)) {
            $total = count($allStudents);
            $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

            if ($page > $totalPages && $total > 0) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $perPage;
            $data = array_slice($allStudents, $offset, $perPage);

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
        }

        $searchLower = strtolower($searchParam);
        $filteredResults = array_values(array_filter($allStudents, function ($student) use ($searchLower) {
            return str_contains(strtolower($student->nis ?? ''), $searchLower) ||
                str_contains(strtolower($student->name ?? ''), $searchLower) ||
                str_contains(strtolower($student->rayon ?? ''), $searchLower);
        }));

        $total = count($filteredResults);
        $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

        if ($page > $totalPages && $total > 0) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $data = array_slice($filteredResults, $offset, $perPage);

        $hasData = !empty($data);
        $from = $hasData && $total > 0 ? $offset + 1 : 0;
        $to = $hasData ? $offset + count($data) : 0;

        $result = [
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

        return $result;
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
            'students_all_*',
            'students_all_data_*'
        ];

        foreach ($cacheKeys as $pattern) {
            Cache::flush();
        }
    }

    public function resetData()
    {
        try {
            $data = Student::truncate();
            $this->clearStudentCache();

            return $data;
        } catch (\Throwable $th) {
            throw new \Exception('Failed to reset student data: ' . $th->getMessage());
        }
    }
}
