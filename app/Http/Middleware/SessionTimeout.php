<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout {
    protected $session;
    protected $timeout;

    public function __construct(Store $session)
    {
        $this->session = $session;
        $this->timeout = config('session.timeout', 15); // 2 seconds
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $lastActivity = $this->session->get('last_activity');
        $currentTime = Carbon::now();

        if ($lastActivity) {
            $lastActivityTime = Carbon::createFromTimestamp($lastActivity);
            $inactiveTime = $currentTime->diffInSeconds($lastActivityTime);

            if ($inactiveTime > $this->timeout) {
                // Force logout
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'Session expired',
                        'redirect' => route('login'),
                        'code' => 419
                    ], 419);
                }

                return redirect()->route('login')
                    ->with('timeout', true)
                    ->with('message', 'Your session has expired due to inactivity.');
            }
        }

        $this->session->put('last_activity', $currentTime->timestamp);
        return $next($request);
    }
}