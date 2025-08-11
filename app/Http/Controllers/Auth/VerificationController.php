<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class VerificationController extends Controller
{
    const OTP_EXPIRY_MINUTES = 6000;
    const OTP_LENGTH = 6;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|array|size:6',
            'otp.*' => 'required|string|size:1|regex:/^[0-9]$/'
        ]);

        $otp = implode('', $request->otp);
        $userId = auth()->id();
        $cacheKey = $this->getOtpCacheKey($userId);

        if (!Cache::has($cacheKey)) {
            return back()->with('error', 'OTP has expired. Please request a new one.');
        }

        if (Cache::get($cacheKey) !== $otp) {
            return back()->with('error', 'Invalid OTP code.');
        }

        $this->markEmailAsVerified($userId);

        return redirect()->route('dashboard')
            ->with('success', 'Your account has been verified!');
    }

    public function resend(Request $request)
    {
        $userId = auth()->id();
        
        if ($this->isInCooldownPeriod($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting a new code.'
            ], 429);
        }

        $otp = $this->generateOtp();
        $this->storeOtp($userId, $otp);
        $this->sendOtpEmail($request->user(), $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully'
        ]);
    }

    private function generateOtp()
    {
        return str_pad(random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    private function getOtpCacheKey($userId)
    {
        return 'otp_' . $userId;
    }

    private function getCooldownCacheKey($userId)
    {
        return 'otp_cooldown_' . $userId;
    }

    private function isInCooldownPeriod($userId)
    {
        return Cache::has($this->getCooldownCacheKey($userId));
    }

    private function storeOtp($userId, $otp)
    {
        Cache::put(
            $this->getOtpCacheKey($userId),
            $otp,
            now()->addMinutes(self::OTP_EXPIRY_MINUTES)
        );

        Cache::put(
            $this->getCooldownCacheKey($userId),
            true,
            now()->addMinute()
        );
    }

    private function sendOtpEmail($user, $otp)
    {
        Mail::to($user->email)->send(new OtpMail($otp, self::OTP_EXPIRY_MINUTES));
    }

    private function markEmailAsVerified($userId)
    {
        $user = auth()->user();
        $user->email_verified_at = now();
        $user->save();

        Cache::forget($this->getOtpCacheKey($userId));
    }
}