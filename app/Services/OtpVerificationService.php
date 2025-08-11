<?php

namespace App\Http\Controllers;

use App\Services\OtpVerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\VerifyOtpRequest;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{
    private $otpService;
    
    public function __construct(OtpVerificationService $otpService)
    {
        $this->otpService = $otpService;
    }
    
    /**
     * Generate and send OTP to user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateOtp(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $otp = $this->otpService->generateOtp($user->id);
            
            // Send OTP via email
            Mail::to($user->email)->send(new OTPMail($otp));
            
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully'
            ]);
            
        } catch (Exception $e) {
            Log::error('OTP Generation failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate OTP. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Verify submitted OTP
     *
     * @param VerifyOtpRequest $request
     * @return JsonResponse
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $result = $this->otpService->verifyOtp(
                $request->user()->id,
                $request->input('otp')
            );
            
            return response()->json($result, $result['success'] ? 200 : 400);
            
        } catch (Exception $e) {
            Log::error('OTP Verification failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Verification failed. Please try again.'
            ], 500);
        }
    }
}