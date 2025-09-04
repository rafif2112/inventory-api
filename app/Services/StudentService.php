<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentService
{
    // public function getAllStudents()
    // {
    //     $search = trim(request()->query('search', ''));

    //     $studentsTimestamp = Student::max('updated_at') ?? now();
    //     $dataVersion = md5($studentsTimestamp);

    //     $allDataCacheKey = 'students_all_' . $dataVersion;

    //     $allStudents = Cache::remember($allDataCacheKey, now()->addHours(1), function () {
    //         return DB::select("
    //             SELECT
    //                 students.*,
    //                 majors.id AS major_id,
    //                 majors.name AS major_name,
    //                 majors.icon,
    //                 majors.color
    //             FROM 
    //                 students
    //             LEFT JOIN 
    //                 majors ON students.major_id = majors.id
    //             ORDER BY students.nis ASC
    //         ");
    //     });

    //     if (empty($search)) {
    //         return $allStudents;
    //     }

    //     $searchLower = strtolower($search);
    //     $filteredResults = array_values(array_filter($allStudents, function ($student) use ($searchLower) {
    //         return str_contains(strtolower($student->nis ?? ''), $searchLower) ||
    //             str_contains(strtolower($student->name ?? ''), $searchLower) ||
    //             str_contains(strtolower($student->rayon ?? ''), $searchLower);
    //     }));

    //     return $filteredResults;
    // }

    // public function getStudentData($search = '', $sortMajor = 'asc', $page = 1, $perPage = 10)
    // {
    //     $page = max(1, (int) $page);
    //     $perPage = max(1, (int) $perPage);
    //     $searchParam = trim($search);
    //     $sortOrder = strtolower($sortMajor) === 'desc' ? 'DESC' : 'ASC';

    //     $latestStudentTimestamp = Student::latest('updated_at')->value('updated_at');
    //     $dataVersion = md5($latestStudentTimestamp);

    //     // Include sort parameter in cache key
    //     $allDataCacheKey = 'students_all_data_' . $dataVersion . '_' . md5($sortMajor);

    //     $allStudents = Cache::remember($allDataCacheKey, now()->addHours(1), function () use ($sortOrder) {
    //         return DB::select("
    //         SELECT
    //             students.*,
    //             majors.id AS major_id,
    //             majors.name AS major_name,
    //             majors.icon,
    //             majors.color
    //         FROM
    //             students
    //         LEFT JOIN
    //             majors ON students.major_id = majors.id
    //         ORDER BY 
    //             CASE WHEN majors.name IS NULL THEN 1 ELSE 0 END,
    //             majors.name {$sortOrder},
    //             students.nis ASC
    //     ");
    //     });

    //     // Apply search filter if provided
    //     if (!empty($searchParam)) {
    //         $searchLower = strtolower($searchParam);
    //         $allStudents = array_values(array_filter($allStudents, function ($student) use ($searchLower) {
    //             return str_contains(strtolower($student->nis ?? ''), $searchLower) ||
    //                 str_contains(strtolower($student->name ?? ''), $searchLower) ||
    //                 str_contains(strtolower($student->rayon ?? ''), $searchLower);
    //         }));

    //         // Re-apply sort after filtering since array_filter can change order
    //         usort($allStudents, function ($a, $b) use ($sortOrder) {
    //             // Handle null major names
    //             if (empty($a->major_name) && empty($b->major_name)) return 0;
    //             if (empty($a->major_name)) return 1;
    //             if (empty($b->major_name)) return -1;

    //             $comparison = strcmp(strtolower($a->major_name), strtolower($b->major_name));
    //             return $sortOrder === 'DESC' ? -$comparison : $comparison;
    //         });
    //     }

    //     // Calculate pagination
    //     $total = count($allStudents);   
    //     $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

    //     if ($page > $totalPages && $total > 0) {
    //         $page = $totalPages;
    //     }

    //     $offset = ($page - 1) * $perPage;
    //     $data = array_slice($allStudents, $offset, $perPage);

    //     $hasData = !empty($data);
    //     $from = $hasData && $total > 0 ? $offset + 1 : 0;
    //     $to = $hasData ? $offset + count($data) : 0;

    //     return [
    //         'data' => $data,
    //         'meta' => [
    //             'current_page' => (int) $page,
    //             'from' => (int) $from,
    //             'last_page' => (int) $totalPages,
    //             'per_page' => (int) $perPage,
    //             'to' => (int) $to,
    //             'total' => (int) $total,
    //         ]
    //     ];
    // }

    public function getStudentData($search = '', $sortMajor = 'asc', $page = 1, $perPage = 10)
    {
        $page = $search ? 1 : $page;

        $query = Student::select('students.*')
            ->with('major')
            ->leftJoin('majors', 'students.major_id', '=', 'majors.id')
            ->when(!empty(trim($search)), function ($q) use ($search) {
                $searchLower = strtolower(trim($search));
                $q->where(function ($q2) use ($searchLower) {
                    $q2->where('students.nis', 'ILIKE', '%' . $searchLower . '%')
                        ->orWhere('students.name', 'ILIKE', '%' . $searchLower . '%')
                        ->orWhere('students.rayon', 'ILIKE', '%' . $searchLower . '%');
                });
            })
            ->orderByRaw('CASE WHEN majors.name IS NULL THEN 1 ELSE 0 END')
            ->when($sortMajor, function ($q) use ($sortMajor) {
                $q->orderBy('majors.name', $sortMajor);
            })
            ->orderBy('students.nis', 'asc');

        $students = $query->paginate($perPage, ['*'], 'page', $page);

        return $students;
    }

    public function getAllStudents($search = '')
    {
        $query = Student::select('students.*')
            ->with('major')
            ->leftJoin('majors', 'students.major_id', '=', 'majors.id')
            ->when(!empty($search), function ($q) use ($search) {
                $q->where('students.nis', 'ILIKE', '%' . $search . '%')
                    ->orWhere('students.name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('students.rayon', 'ILIKE', '%' . $search . '%');
            })
            ->orderBy('students.nis', 'asc');

        $students = $query->get();

        return $students;
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
