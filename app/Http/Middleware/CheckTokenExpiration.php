<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\TokenExpiredException;
use Laravel\Passport\Token;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            $token = $user->token();
            
            if ($token && $token->expires_at && $token->expires_at->isPast()) {
                // Token sudah expired, hapus token
                $token->delete();
                throw new TokenExpiredException();
            }
        }

        return $next($request);
    }
}