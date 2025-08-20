<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getLoginUser()
    {
        return auth()->user()->only(['id', 'name', 'username', 'role', 'major_id']);
    }

    public function getUsersWithMajorPaginate(string $search = '', string $sortDir = 'asc', int $perPage = 10)
    {
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? $sortDir : 'asc';

        return User::with('major')
            ->select('users.*')
            ->leftJoin('majors', 'users.major_id', '=', 'majors.id')
            ->when($search, function ($query) use ($search) {
                return $query->where('users.name', 'ilike', "%{$search}%");
            })
            ->orderBy('majors.id', $sortDir)
            ->paginate($perPage);
    }

    public function createUser(array $data)
    {
        try {
            $newUser = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'role' => $data['role'],
                'password' => Hash::make($data['password']),
                'major_id' => $data['major_id'] ?? null,
            ]);

            return $newUser;
        } catch (\Throwable $th) {
            Log::error('Failed to create User: ' . $th->getMessage());
            throw $th;
        }
    }

    public function getUserById(User $user)
    {
        $user->Find('user');
        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        try {
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'username' => $data['username'] ?? $user->username,
                'role' => $data['role'] ?? $user->role,
                'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password,
                'major_id' => $data['major_id'] ?? $user->major_id,
            ]);

            return $user;
        } catch (\Throwable $th) {
            Log::error('Failed to update User: ' . $th->getMessage());
            throw $th;
        }
    }

    public function deleteUser(User $user)
    {
        try {
            $user->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error('Failed to delete User: ' . $th->getMessage());
            throw $th;
        }
    }
}
