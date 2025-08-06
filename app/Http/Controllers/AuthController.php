<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();

        return response()->json([
            'status' => 'success',
            'data' => [
                'user_id' => $userId,
                'name' => Auth::user()->name,
                'username' => Auth::user()->username,
                'role' => Auth::user()->role,
            ]
        ]);
    }

    public function getLimiterKey(Request $request) {
        return RateLimiter::availableIn($request->ip());
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $limiterKey = $this->getLimiterKey($request);

        if (RateLimiter::tooManyAttempts($limiterKey, 3)) {
            $retryAfter = RateLimiter::availableIn($limiterKey);
            return response()->json([
                'status' => 429,
                'message' => "Too many attempts. Try again in $retryAfter seconds.",
            ], 429);
        }

        RateLimiter::hit($limiterKey, 60);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = Auth::user();

        User::whereId(Auth::id());
        // Clear rate limiter on successful login
        RateLimiter::clear($limiterKey);

        return response()->json([
            'status' => 200,
            'data' => [
                'usid' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
            ],
            'token' => $user->createToken('wikventory')->accessToken,
        ], 200);
    }

    public function logout()
    {
        Auth::user()->token()->delete();
        Auth::guard('web')->logout();
        User::whereId(Auth::id());
        return response()->json([
            'status' => 'success',
            'message' => "berhasil logout"
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
