<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VerifyEmailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return $this->verifyOtp($request);
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:6,1')->only('verifyOtp', 'send');
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(RouteServiceProvider::HOME)
            : view('auth.verify-email');
    }

    /**
     * Verify the email using provided OTP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOtp(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Validate input
        $request->validate([
            'otp' => ['required', 'array', 'size:6'],
            'otp.*' => ['required', 'numeric', 'digits:1'],
        ]);

        // Combine OTP digits
        $submittedOtp = implode('', $request->input('otp'));

        // Verify OTP
        if (
            $user->verification_code === $submittedOtp &&
            $user->verification_code_expires_at > now()
        ) {
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            $user->verification_code = null;
            $user->verification_code_expires_at = null;
            $user->save();

            return redirect()->intended(RouteServiceProvider::HOME)
                ->with('success', 'Email verified successfully!');
        }

        return back()->with('error', 'Invalid or expired verification code.');
    }

    /**
     * Send a new verification code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Check for cooldown
        if (session('last_otp_sent')) {
            $lastSent = Carbon::parse(session('last_otp_sent'));
            $cooldownSeconds = 30;

            if ($lastSent->addSeconds($cooldownSeconds) > now()) {
                $remainingTime = $lastSent->addSeconds($cooldownSeconds)->diffInSeconds(now());
                session(['resend_cooldown' => $remainingTime]);

                return back()->with('error', 'Please wait before requesting another code.');
            }
        }

        
        // Generate OTP
        $otp = sprintf('%06d', random_int(0, 999999));

        // Store OTP in database
        $user->verification_code = $otp;
        $user->verification_code_expires_at = now()->addMinutes(10);
        $user->save();

        // Send OTP email
        $user->notify(new \App\Notifications\NewUserRegistration($user, $otp));
        session(['last_otp_sent' => now()]);

        return back()->with('status', 'A new verification code has been sent to your email address.');
    }
}