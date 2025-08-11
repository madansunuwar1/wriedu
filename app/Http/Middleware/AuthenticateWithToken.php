<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticateWithToken
{
    public function handle(Request $request, Closure $next)
    {
        // Check for token in Authorization header
        $token = $request->bearerToken();
        
        if (!$token) {
            // Check for token in request parameters (for development)
            $token = $request->input('token');
        }
        
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        // Find the token
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Check if token is expired (if you're using expiration)
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json(['error' => 'Token expired'], 401);
        }

        // Set the authenticated user
        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->tokenable;
        });

        return $next($request);
    }
}