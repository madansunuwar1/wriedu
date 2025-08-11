<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class OTPVerificationController extends Controller
{
    private const MAX_ATTEMPTS = 3;
    private const BLOCK_DURATION = 15; // minutes
    private const OTP_LIFETIME = 10; // minutes
    
    /**
     * Handle the OTP verification request
     */
    public function verify(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'otp' => ['required', 'array', 'size:6'],
                'otp.*' => ['required', 'string', 'size:1', 'regex:/^[0-9]$/'],
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $submittedOtp = implode('', $request->otp);
            
            // Log verification attempt
            Log::info('Starting OTP verification', [
                'user_id' => $user->id,
                'submitted_otp_length' => strlen($submittedOtp)
            ]);

            // Check if user is blocked
            if ($this->isUserBlocked((string)$user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many attempts. Please try again after ' . self::BLOCK_DURATION . ' minutes.'
                ], 429);
            }

            // Get stored OTP and expiration time
            $otpData = $this->getStoredOtpWithExpiration((string)$user->id);
            
            if (!$otpData) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active OTP found. Please request a new code.'
                ], 422);
            }

            // Check expiration
            if (Carbon::now()->isAfter($otpData['expires_at'])) {
                $this->clearOtpData((string)$user->id);
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new code.'
                ], 422);
            }

            // Verify OTP
            if ($submittedOtp !== $otpData['otp']) {
                $this->incrementAttempts((string)$user->id);
                
                $remainingAttempts = self::MAX_ATTEMPTS - $this->getAttempts((string)$user->id);
                
                if ($remainingAttempts <= 0) {
                    $this->blockUser((string)$user->id);
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum attempts exceeded. Please try again after ' . self::BLOCK_DURATION . ' minutes.'
                    ], 429);
                }

                return response()->json([
                    'success' => false,
                    'message' => "Invalid OTP. You have {$remainingAttempts} attempts remaining."
                ], 422);
            }

            // Success case
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            $this->clearOtpData((string)$user->id);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
              'redirect' => '/app/dashboard'
            ]);

        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during verification. Please try again.'
            ], 500);
        }
    }





    private function getStoredOtpWithExpiration(string $userId): ?array
    {
        // Try cache first
        $cacheKey = $this->getOtpCacheKey($userId);
        $storedOtp = Cache::get($cacheKey);
        $expiresAt = Cache::get($cacheKey . '_expires');

        // If not in cache, check database
        if (!$storedOtp || !$expiresAt) {
            $otpRecord = DB::table('otps')
                ->where('user_id', $userId)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if ($otpRecord) {
                $storedOtp = $otpRecord->otp;
                $expiresAt = Carbon::parse($otpRecord->expires_at);
                
                // Restore cache
                Cache::put($cacheKey, $storedOtp, $expiresAt);
                Cache::put($cacheKey . '_expires', $expiresAt, $expiresAt);
            }
        }

        if (!$storedOtp || !$expiresAt) {
            Log::info('No valid OTP found', [
                'user_id' => $userId,
                'cache_key' => $cacheKey
            ]);
            return null;
        }

        return [
            'otp' => $storedOtp,
            'expires_at' => $expiresAt
        ];
    }







    /**
     * Handle OTP resend request
     */
    public function resend(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if user is blocked
            if ($this->isUserBlocked((string)$user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many attempts. Please try again after ' . self::BLOCK_DURATION . ' minutes.'
                ], 429);
            }

            // Generate new OTP
            $otp = $this->generateOtp((string)$user->id);

            // Send OTP email
            Mail::to($user->email)->send(new OTPMail($otp));

            return response()->json([
                'success' => true,
                'message' => 'A new verification code has been sent to your email.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to resend OTP', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.'
            ], 500);
        }
    }

    /**
     * Generate and store a new OTP
     */
    public function generateOtp(string $userId): string
    {
        // Clear any existing OTP first
        $this->clearOtpData($userId);
        
        // Generate a 6-digit OTP
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        try {
            // Store the new OTP
            $this->storeOtp($userId, $otp);
            
            // Reset attempts counter
            $this->resetAttempts($userId);
            
            Log::info('New OTP generated and stored', [
                'user_id' => $userId,
                'cache_key' => $this->getOtpCacheKey($userId)
            ]);
            
            return $otp;
            
        } catch (\Exception $e) {
            Log::error('OTP Generation Failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Store OTP in cache with expiration
     */
    private function storeOtp(string $userId, string $otp): void
    {
        $expiresAt = Carbon::now()->addMinutes(self::OTP_LIFETIME);
        
        // Store in cache with expiration time
        $cacheKey = $this->getOtpCacheKey($userId);
        Cache::put($cacheKey, $otp, $expiresAt);
        Cache::put($cacheKey . '_expires', $expiresAt, $expiresAt);
        
        // Store in database
        DB::table('otps')->updateOrInsert(
            ['user_id' => $userId],
            [
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'updated_at' => Carbon::now()
            ]
        );

        // Verify storage
        $otpData = $this->getStoredOtpWithExpiration($userId);
        
        Log::info('OTP Storage Verification', [
            'user_id' => $userId,
            'cache_key' => $cacheKey,
            'storage_successful' => ($otpData && $otpData['otp'] === $otp),
            'expiration_time' => $expiresAt->toDateTimeString()
        ]);

        if (!$otpData || $otpData['otp'] !== $otp) {
            throw new \RuntimeException('Failed to store OTP');
        }
    }

        
    /**
     * Verify submitted OTP
     */
    public function verifyOtp(string $userId, string $submittedOtp): array
    {
        // Check if user is blocked
        if ($this->isUserBlocked($userId)) {
            return [
                'success' => false,
                'message' => 'Too many failed attempts. Please try again after ' . self::BLOCK_DURATION . ' minutes.'
            ];
        }

        // Get stored OTP
        $storedOtp = $this->getStoredOtp($userId);
        
        // Log verification attempt
        Log::info('OTP Verification Check', [
            'submitted_otp' => $submittedOtp,
            'stored_otp' => $storedOtp,
            'cache_key' => $this->getOtpCacheKey($userId)
        ]);

        // Validate OTP
        if ($storedOtp === null) {
            return [
                'success' => false,
                'message' => 'OTP has expired or is invalid'
            ];
        }

        if ($submittedOtp !== $storedOtp) {
            $this->incrementAttempts($userId);
            
            // Check if max attempts reached
            if ($this->getAttempts($userId) >= self::MAX_ATTEMPTS) {
                $this->blockUser($userId);
                return [
                    'success' => false,
                    'message' => 'Maximum attempts exceeded. Please try again after ' . self::BLOCK_DURATION . ' minutes.'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Invalid OTP'
            ];
        }

        // Clear OTP and attempts after successful verification
        $this->clearOtpData($userId);
        
        return [
            'success' => true,
            'message' => 'OTP verified successfully'
        ];
    }
    
    /**
     * Get stored OTP from cache
     */
    private function getStoredOtp(string $userId): ?string
    {
        // Try cache first
        $cacheKey = $this->getOtpCacheKey($userId);
        $storedOtp = Cache::get($cacheKey);
        
        // If not in cache, check database
        if (!$storedOtp) {
            $otpRecord = DB::table('otps')
                ->where('user_id', $userId)
                ->where('expires_at', '>', Carbon::now())
                ->first();
                
            if ($otpRecord) {
                $storedOtp = $otpRecord->otp;
                // Restore cache
                Cache::put($cacheKey, $storedOtp, Carbon::parse($otpRecord->expires_at));
            }
        }
        
        Log::info('Retrieving stored OTP', [
            'user_id' => $userId,
            'cache_key' => $cacheKey,
            'otp_found' => !is_null($storedOtp)
        ]);
        
        return $storedOtp;
    }
    
    /**
     * Get OTP cache key
     */
    private function getOtpCacheKey(string $userId): string
    {
        return "otp_{$userId}";
    }
    
    /**
     * Get attempts cache key
     */
    private function getAttemptsKey(string $userId): string
    {
        return "otp_attempts_{$userId}";
    }
    
    /**
     * Get block cache key
     */
    private function getBlockKey(string $userId): string
    {
        return "otp_block_{$userId}";
    }
    
    /**
     * Increment failed attempts
     */
    private function incrementAttempts(string $userId): void
    {
        $key = $this->getAttemptsKey($userId);
        $attempts = Cache::get($key, 0);
        Cache::put($key, $attempts + 1, Carbon::now()->addMinutes(self::OTP_LIFETIME));
    }
    
    /**
     * Get current attempts count
     */
    private function getAttempts(string $userId): int
    {
        return Cache::get($this->getAttemptsKey($userId), 0);
    }
    
    /**
     * Reset attempts counter
     */
    private function resetAttempts(string $userId): void
    {
        Cache::forget($this->getAttemptsKey($userId));
    }
    
    /**
     * Block user
     */
    private function blockUser(string $userId): void
    {
        Cache::put(
            $this->getBlockKey($userId),
            true,
            Carbon::now()->addMinutes(self::BLOCK_DURATION)
        );
    }
    
    /**
     * Check if user is blocked
     */
    private function isUserBlocked(string $userId): bool
    {
        return Cache::has($this->getBlockKey($userId));
    }
    
    /**
     * Clear OTP related data after successful verification
     */
    private function clearOtpData(string $userId): void
    {
        Cache::forget($this->getOtpCacheKey($userId));
        Cache::forget($this->getAttemptsKey($userId));
        Cache::forget($this->getBlockKey($userId));
        
        DB::table('otps')->where('user_id', $userId)->delete();
    }
     
    
    public function sendInitialOtp(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Clear any existing OTP data
            $this->clearOtpData((string)$user->id);

            // Generate and store new OTP
            $otp = $this->generateOtp((string)$user->id);

            // Verify OTP storage before sending email
            $otpData = $this->getStoredOtpWithExpiration((string)$user->id);
            if (!$otpData) {
                throw new \RuntimeException('Failed to store OTP');
            }

            // Send OTP email
            Mail::to($user->email)->send(new OTPMail($otp));

            Log::info('Initial OTP sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification code has been sent to your email.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send initial OTP', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.'
            ], 500);
        }
    }
  







    public function forward()
    {
        return view('emails.document_forwarded');
    }

}