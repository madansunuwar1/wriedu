<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Laravel\Sanctum\PersonalAccessToken;

class BroadcastController extends Controller
{
    public function authenticate(Request $request)
    {
        // Get the token from Authorization header
        $token = $request->bearerToken();
        
        if (!$token) {
            return response(['error' => 'Token not provided'], 401);
        }

        // Find the token
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken || ($accessToken->expires_at && $accessToken->expires_at->isPast())) {
            return response(['error' => 'Invalid or expired token'], 401);
        }

        // Set the authenticated user
        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->tokenable;
        });

        return Broadcast::auth($request);
    }
}