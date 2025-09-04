<?php

namespace App\Http\Controllers;

use App\Exceptions\TokenExpiredException;
use App\Http\Resources\UserResource;
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
        $user = Auth::user();

        $this->checkTokenExpiration($user);

        return response()->json([
            'status' => 200,
            'data' => new UserResource($user)
        ], 200);
    }

    private function checkTokenExpiration($user)
    {
        $token = $user->token();
        
        if ($token && $token->expires_at && $token->expires_at->isPast()) {
            $token->delete();
            throw new TokenExpiredException();
        }
    }

    public function getLimiterKey(Request $request)
    {
        return RateLimiter::availableIn($request->ip());
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'status' => 200,
                'message' => 'User already logged in.',
            ]);
        }

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

        $tokenResult = $user->createToken('wikventory');
        $token = $tokenResult->token;
        $token->expires_at = now()->addHour();
        $token->save();

        return response()->json([
            'status' => 200,
            'data' => [
                'usid' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
                'major_id' => $user->major_id,
            ],
            'token' => $tokenResult->accessToken,
            'expires_at' => $token->expires_at,
        ], 200);
    }

    public function logout()
    {
        Auth::user()->token()->delete();
        Auth::guard('web')->logout();
        User::whereId(Auth::id());
        return response()->json([
            'status' => 200,
            'message' => "berhasil logout"
        ], 200);
    }

    public function checkToken()
    {
        $user = Auth::user();
        $token = $user->token();
        
        if (!$token) {
            throw new TokenExpiredException('No token found');
        }
        
        if ($token->expires_at && $token->expires_at->isPast()) {
            $token->delete();
            throw new TokenExpiredException();
        }
        
        return response()->json([
            'status' => 200,
            'message' => 'Token is valid',
            'expires_at' => $token->expires_at->toISOString(),
            'remaining_time' => $token->expires_at->diffForHumans()
        ], 200);
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
