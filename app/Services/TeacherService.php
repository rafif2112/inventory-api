<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherService
{
    // public function getAllTeachers($search = '')
    // {
    //     $searchParam = trim($search);

    //     $teachersTimestamp = Teacher::max('updated_at') ?? now();
    //     $dataVersion = md5($teachersTimestamp);

    //     $allDataCacheKey = 'teachers_all_' . $dataVersion;

    //     $allTeachers = Cache::remember($allDataCacheKey, now()->addHours(1), function () {
    //         return DB::select("
    //             SELECT * FROM teachers
    //             ORDER BY teachers.name ASC
    //         ");
    //     });

    //     if (empty($searchParam)) {
    //         return $allTeachers;
    //     }

    //     $searchLower = strtolower($searchParam);
    //     $filteredResults = array_values(array_filter($allTeachers, function ($teacher) use ($searchLower) {
    //         return str_contains(strtolower($teacher->nip ?? ''), $searchLower) ||
    //             str_contains(strtolower($teacher->name ?? ''), $searchLower) ||
    //             str_contains(strtolower($teacher->telephone ?? ''), $searchLower);
    //     }));

    //     return $filteredResults;
    // }

    // public function getTeachersData($search = '', $page = 1, $perPage = 10)
    // {
    //     $page = max(1, (int) $page);
    //     $perPage = max(1, (int) $perPage);
    //     $searchParam = trim($search);

    //     $latestTeacherTimestamp = Teacher::latest('updated_at')->value('updated_at');
    //     $dataVersion = md5($latestTeacherTimestamp);

    //     $allDataCacheKey = 'teachers_all_data_' . $dataVersion;

    //     $allTeachers = Cache::remember($allDataCacheKey, now()->addHours(1), function () {
    //         return DB::select("
    //             SELECT * FROM teachers
    //             ORDER BY teachers.name ASC
    //         ");
    //     });

    //     if (empty($searchParam)) {
    //         $total = count($allTeachers);
    //         $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

    //         if ($page > $totalPages && $total > 0) {
    //             $page = $totalPages;
    //         }

    //         $offset = ($page - 1) * $perPage;
    //         $data = array_slice($allTeachers, $offset, $perPage);

    //         $hasData = !empty($data);
    //         $from = $hasData && $total > 0 ? $offset + 1 : 0;
    //         $to = $hasData ? $offset + count($data) : 0;

    //         return [
    //             'data' => $data,
    //             'meta' => [
    //                 'current_page' => (int) $page,
    //                 'from' => (int) $from,
    //                 'last_page' => (int) $totalPages,
    //                 'per_page' => (int) $perPage,
    //                 'to' => (int) $to,
    //                 'total' => (int) $total,
    //             ]
    //         ];
    //     }

    //     $searchLower = strtolower($searchParam);
    //     $filteredResults = array_values(array_filter($allTeachers, function ($teacher) use ($searchLower) {
    //         return str_contains(strtolower($teacher->nip ?? ''), $searchLower) ||
    //             str_contains(strtolower($teacher->name ?? ''), $searchLower) ||
    //             str_contains(strtolower($teacher->telephone ?? ''), $searchLower);
    //     }));

    //     $total = count($filteredResults);
    //     $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

    //     if ($page > $totalPages && $total > 0) {
    //         $page = $totalPages;
    //     }

    //     $offset = ($page - 1) * $perPage;
    //     $data = array_slice($filteredResults, $offset, $perPage);

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

    public function getAllTeachers($search = '')
    {
        $data = Teacher::select('teachers.*')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('nip', 'ILIKE', '%' . $search . '%')
                        ->orWhere('name', 'ILIKE', '%' . $search . '%')
                        ->orWhere('telephone', 'ILIKE', '%' . $search . '%');
                });
            })
            ->orderBy('name', 'ASC')
            ->get();

        return $data;
    }

    public function getTeachersData($search = '', $request)
    {
        $page = $search ? 1 : $request->query('page', 1);
        $perPage = 10;

        $data = Teacher::select('teachers.*')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('nip', 'ILIKE', '%' . $search . '%')
                        ->orWhere('name', 'ILIKE', '%' . $search . '%')
                        ->orWhere('telephone', 'ILIKE', '%' . $search . '%');
                });
            })
            ->orderBy('name', 'ASC')
            ->paginate($perPage, ['*'], 'page', $page);

        return $data;
    }

    public function createTeacher(array $data)
    {
        $teacher = Teacher::create([
            'nip' => $data['nip'],
            'name' => $data['nama'],
            'telephone' => $data['no_telp'],
        ]);

        $this->clearTeacherCache();

        return $teacher;
    }

    public function updateTeacher(Teacher $teacher, array $data)
    {
        if ($teacher) {
            $teacher->update([
                'nip' => $data['nip'],
                'name' => $data['nama'],
                'telephone' => $data['no_telp'],
            ]);

            $this->clearTeacherCache();

            return $teacher;
        }

        return null;
    }

    public function getTeacherById($id)
    {
        return Teacher::find($id);
    }

    public function deleteTeacher(Teacher $teacher)
    {
        if ($teacher) {
            try {
                $teacher->delete();
                $this->clearTeacherCache();
                return true;
            } catch (\Exception $e) {
                Log::error('Failed to delete teacher: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    private function clearTeacherCache()
    {
        $cacheKeys = [
            'teachers_all_*',
            'teachers_all_data_*'
        ];

        foreach ($cacheKeys as $pattern) {
            Cache::flush();
        }
    }

    public function resetTeachersData()
    {
        try {
            Teacher::truncate();
            $this->clearTeacherCache();

            return true;
        } catch (\Throwable $th) {
            Log::error('Failed to reset teacher data: ' . $th->getMessage());
            return false;
        }
    }
}
