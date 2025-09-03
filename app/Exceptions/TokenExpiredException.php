<?php

namespace App\Exceptions;

use Exception;

class TokenExpiredException extends Exception
{
    protected $message = 'Token has expired. Please login again.';
    protected $code = 401;

    public function render($request)
    {
        return response()->json([
            'status' => 401,
            'message' => $this->message,
            'error' => 'TOKEN_EXPIRED'
        ], 401);
    }
}