<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAllUsers()
    {
        return User::with('major')->get();
    }

    public function createUser(array $data)
    {
        try {
            $newUser = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'role' => $data['role'],
                'email_verified_at' => $data['email_verified_at'],
                'password' => Hash::make($data['password']),
                'major_id' => $data['major_id'],
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
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'role' => $data['role'],
                'email_verified_at' => $data['email_verified_at'],
                'password' => $data['password'],
                'major_id' => $data['major_id'],
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

