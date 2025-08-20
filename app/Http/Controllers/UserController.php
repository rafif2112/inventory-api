<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreValidate;
use App\Http\Requests\User\UpdateValidate;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->userService->getLoginUser();

        return response()->json([
            'status' => 200,
            'data' => $data
        ], 200);
    }

    public function indexPaginate(Request $request)
    {

        $search = $request->query('search', '');
        $sortDir = $request->query('sort_dir', 'asc');

        $users = $this->userService->getUsersWithMajorPaginate($search, $sortDir);

        return response()->json([
            'status' => 200,
            'message' => 'Users retrieved successfully',
            'data' => UserResource::collection($users->items()),
            'meta' => new PaginationResource($users)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreValidate $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $newData = $this->userService->createUser($data);

            DB::commit();
            return response()->json([
                'status' => 201,
                // 'data' => $newData,
                'message' => "User created successfully"
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => 'failed to create new data'
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $userData = $this->userService->getUserById($user);

        return response()->json([
            'status' => 200,
            'data' => $userData,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValidate $request, User $user)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $updatedUser = $this->userService->updateUser($user, $data);

            DB::commit();
            return response()->json([
                'status' => 200,
                'data' => $updatedUser,
            ], 200);
        } catch (\Throwable) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update data',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'data not found',
                ], 404);
            }

            $this->userService->deleteUser($user);

            return response()->json([
                'status' => 200,
                'message' => 'data deleted successfully'
            ], 200);
        } catch (\Throwable) {
            return response()->json([
                'status' => 'error',
                'message' => 'failed to delete data'
            ]);
        }
    }
}
