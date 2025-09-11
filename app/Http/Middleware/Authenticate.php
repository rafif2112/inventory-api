<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            abort(401, 'Unauthenticated. Please login first.');
        }
        return route('login');
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json([
            'status' => '401',
            'error' => 'Unauthorized',
            'message' => 'You do not have permission to access this resource.'
        ], 401));
    }
}