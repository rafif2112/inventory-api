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
    public function getAllTeachers($search = '')
    {
        // return Teacher::all();

        // $searchTerm = '%' . (string)($search ?? '') . '%';
        $teachers = DB::select("
            SELECT * FROM teachers
            WHERE 
                teachers.nip::text ILIKE CONCAT('%', ?::text, '%')
            OR 
                teachers.name ILIKE CONCAT('%', ?::text, '%')
            ORDER BY teachers.name ASC
        ", [$search, $search]);

        return $teachers;
    }

    public function getTeachersData($search = '', $page = 1, $perPage = 10)
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $searchParam = trim($search);

        $latestTeacherTimestamp = Teacher::latest('updated_at')->value('updated_at');
        $cacheKey = 'teachers_data_' . md5($searchParam . $page . $latestTeacherTimestamp);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($searchParam, $perPage, $page) {
            $totalCount = DB::select("
                SELECT COUNT(*) as total
                FROM 
                    teachers
                WHERE
                    teachers.nip::text ILIKE CONCAT('%', ?::text, '%')
                OR
                    teachers.name ILIKE CONCAT('%', ?::text, '%')
            ", [$searchParam, $searchParam]);

            $total = $totalCount[0]->total ?? 0;
            $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

            if ($page > $totalPages && $total > 0) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $perPage;

            $teachers = DB::select("
                SELECT * FROM 
                    teachers
                WHERE 
                    teachers.nip::text ILIKE CONCAT('%', ?::text, '%')
                OR 
                    teachers.name ILIKE CONCAT('%', ?::text, '%')
                ORDER BY teachers.name ASC
                LIMIT ? OFFSET ?
            ", [$searchParam, $searchParam, $perPage, $offset]);

            $hasData = !empty($teachers);
            $from = $hasData && $total > 0 ? $offset + 1 : 0;
            $to = $hasData ? $offset + count($teachers) : 0;

            return [
                'data' => $teachers,
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

    public function createTeacher(array $data)
    {
        return Teacher::create([
            'nip' => $data['nip'],
            'name' => $data['nama'],
            'telephone' => $data['no_telp'],
        ]);
    }

    public function updateTeacher(Teacher $teacher, array $data)
    {
        if ($teacher) {
            $teacher->update([
                'nip' => $data['nip'],
                'name' => $data['nama'],
                'telephone' => $data['no_telp'],
            ]);
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
                return true;
            } catch (\Exception $e) {
                Log::error('Failed to delete teacher: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }
}
