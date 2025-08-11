<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Log;

class OTPController extends Controller
{
    private const OTP_TIMEOUT = 600; // 10 minutes in seconds
    private const MAX_ATTEMPTS = 3;
    
    public function showVerification()
    {
        return view('auth.otp-verification');
    }

    public function sendOTP(Request $request)
    {
        try {
            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $userId = auth()->id();
            
            // Store in cache
            Cache::put('otp_' . $userId, [
                'code' => $otp,
                'attempts' => 0,
                'created_at' => now()
            ], now()->addSeconds(self::OTP_TIMEOUT));

            // Send email
            Mail::to($request->user()->email)->send(new OtpMail($otp));

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('OTP Send Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send code. Please try again.'
            ], 500);
        }
    }

    public function verify(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|array|size:6',
                'otp.*' => 'required|string|size:1|regex:/^[0-9]$/'
            ]);

            $userId = auth()->id();
            $cacheKey = 'otp_' . $userId;
            $otpData = Cache::get($cacheKey);

            if (!$otpData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code expired. Please request a new one.'
                ], 400);
            }

            // Check attempts
            if ($otpData['attempts'] >= self::MAX_ATTEMPTS) {
                Cache::forget($cacheKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Too many attempts. Please request a new code.'
                ], 400);
            }

            // Increment attempts
            $otpData['attempts']++;
            Cache::put($cacheKey, $otpData, now()->addSeconds(self::OTP_TIMEOUT));

            // Verify OTP
            $submittedOtp = implode('', $request->otp);
            if ($otpData['code'] !== $submittedOtp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code'
                ], 400);
            }

            // Mark as verified
            $user = auth()->user();
            $user->email_verified_at = now();
            $user->save();

            Cache::forget($cacheKey);

            return response()->json([
                'success' => true,
                'message' => 'Verification successful!',
                'redirect' => '/app/dashboard'
            ]);

        } catch (\Exception $e) {
            Log::error('OTP Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Verification failed. Please try again.'
            ], 500);
        }
    }

    public function resend(Request $request)
    {
        try {
            $userId = auth()->id();
            $resendKey = 'otp_resend_' . $userId;
            
            // Check resend limit
            $resendCount = Cache::get($resendKey, 0);
            if ($resendCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please try again after some time'
                ], 429);
            }

            // Update resend count
            Cache::put($resendKey, $resendCount + 1, now()->addHours(1));
            
            return $this->sendOTP($request);

        } catch (\Exception $e) {
            Log::error('OTP Resend Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend code'
            ], 500);
        }
    }
}