<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthTokenController extends Controller
{
    public function generateToken(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Revoke existing tokens for this user (optional)
        // $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('chat-token', ['chat:read', 'chat:write'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'expires_at' => now()->addDays(30) // Token expires in 30 days
        ]);
    }

    public function refreshToken(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get current token
        $currentToken = $request->user()->currentAccessToken();
        
        if ($currentToken) {
            // Delete current token
            $currentToken->delete();
        }

        // Create new token
        $token = $user->createToken('chat-token-refreshed', ['chat:read', 'chat:write'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'expires_at' => now()->addDays(30)
        ]);
    }

    public function validateToken(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        return response()->json([
            'valid' => true,
            'user' => $user,
            'token_name' => $request->user()->currentAccessToken()->name ?? null,
            'expires_at' => $request->user()->currentAccessToken()->expires_at ?? null
        ]);
    }
}
