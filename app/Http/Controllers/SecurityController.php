<?php
// Backend Security Implementation for Data Protection

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Models\SecurityAuditLog;
use App\Models\UserSession;
use App\Models\SecurityEvent;
use Carbon\Carbon;

// 1. Security Event Controller
class SecurityController extends Controller
{
    protected $riskThresholds = [
        'screenshot_attempts' => 3,
        'copy_attempts' => 5,
        'focus_losses' => 10,
        'suspicious_activity' => 5
    ];

    // Handle security event notifications
    public function recordSecurityEvent(Request $request)
    {
        $validated = $request->validate([
            'eventType' => 'required|string',
            'details' => 'required|array',
            'sessionId' => 'required|string',
            'timestamp' => 'required|integer'
        ]);

        $userSession = UserSession::where('session_id', $validated['sessionId'])->first();
        
        if (!$userSession) {
            return response()->json(['error' => 'Invalid session'], 401);
        }

        // Create security event record
        $securityEvent = SecurityEvent::create([
            'user_id' => $userSession->user_id,
            'session_id' => $validated['sessionId'],
            'event_type' => $validated['eventType'],
            'details' => $validated['details'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => Carbon::createFromTimestamp($validated['timestamp'] / 1000)
        ]);

        // Check risk level and trigger appropriate response
        $this->assessRiskLevel($userSession->user_id, $validated['eventType']);

        return response()->json(['status' => 'recorded']);
    }

    // Comprehensive audit logging
    public function recordAuditLog(Request $request)
    {
        $validated = $request->validate([
            'event' => 'required|string',
            'details' => 'array',
            'sessionId' => 'string',
            'fingerprint' => 'string',
            'url' => 'required|string',
            'timestamp' => 'required|string'
        ]);

        $userSession = null;
        if (isset($validated['sessionId'])) {
            $userSession = UserSession::where('session_id', $validated['sessionId'])->first();
        }

        SecurityAuditLog::create([
            'user_id' => $userSession ? $userSession->user_id : null,
            'session_id' => $validated['sessionId'] ?? null,
            'event' => $validated['event'],
            'details' => $validated['details'] ?? [],
            'fingerprint' => $validated['fingerprint'] ?? null,
            'url' => $validated['url'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => Carbon::parse($validated['timestamp'])
        ]);

        // Real-time risk assessment
        if ($userSession) {
            $this->updateRiskScore($userSession->user_id, $validated['event']);
        }

        return response()->json(['status' => 'logged']);
    }

    // Session validation with device fingerprinting
    public function validateSession(Request $request)
    {
        $validated = $request->validate([
            'fingerprint' => 'required|string',
            'timestamp' => 'required|integer'
        ]);

        $sessionId = $request->session()->getId();
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Check existing session
        $userSession = UserSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->first();

        if (!$userSession) {
            // Create new session record
            $userSession = UserSession::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'device_fingerprint' => $validated['fingerprint'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_activity' => now(),
                'risk_score' => 0
            ]);
        } else {
            // Validate fingerprint hasn't changed
            if ($userSession->device_fingerprint !== $validated['fingerprint']) {
                // Potential session hijacking
                $this->logSuspiciousActivity($userId, 'FINGERPRINT_MISMATCH', [
                    'original' => $userSession->device_fingerprint,
                    'current' => $validated['fingerprint']
                ]);
                
                return response()->json(['error' => 'Session validation failed'], 401);
            }

            // Update last activity
            $userSession->update(['last_activity' => now()]);
        }

        // Check for multiple concurrent sessions
        $activeSessions = UserSession::where('user_id', $userId)
            ->where('last_activity', '>', now()->subMinutes(30))
            ->count();

        if ($activeSessions > 2) {
            $this->logSuspiciousActivity($userId, 'MULTIPLE_SESSIONS', [
                'session_count' => $activeSessions
            ]);
        }

        return response()->json([
            'session' => [
                'id' => $sessionId,
                'risk_score' => $userSession->risk_score,
                'last_activity' => $userSession->last_activity
            ]
        ]);
    }

    // Risk assessment and scoring
    protected function assessRiskLevel($userId, $eventType)
    {
        $cacheKey = "user_risk_{$userId}";
        $currentEvents = Redis::get($cacheKey);
        $currentEvents = $currentEvents ? json_decode($currentEvents, true) : [];

        // Add current event
        $currentEvents[] = [
            'type' => $eventType,
            'timestamp' => time()
        ];

        // Keep only events from last hour
        $currentEvents = array_filter($currentEvents, function($event) {
            return $event['timestamp'] > (time() - 3600);
        });

        // Count events by type
        $eventCounts = array_count_values(array_column($currentEvents, 'type'));

        // Calculate risk score
        $riskScore = 0;
        foreach ($eventCounts as $type => $count) {
            $threshold = $this->riskThresholds[$type] ?? 10;
            if ($count >= $threshold) {
                $riskScore += ($count - $threshold + 1) * 10;
            }
        }

        // Store updated events
        Redis::setex($cacheKey, 3600, json_encode($currentEvents));

        // Update user session risk score
        UserSession::where('user_id', $userId)
            ->update(['risk_score' => $riskScore]);

        // Trigger alerts for high risk
        if ($riskScore >= 50) {
            $this->triggerHighRiskAlert($userId, $riskScore, $eventCounts);
        }

        return $riskScore;
    }

    protected function updateRiskScore($userId, $event)
    {
        $riskIncrements = [
            'SCREENSHOT_ATTEMPT' => 15,
            'LARGE_TEXT_COPY' => 10,
            'AUTOMATION_SUSPECTED' => 20,
            'SYSTEM_TAMPERING_DETECTED' => 30,
            'UNAUTHORIZED_NETWORK_REQUEST' => 25,
            'MULTIPLE_FOCUS_LOSSES' => 5
        ];

        $increment = $riskIncrements[$event] ?? 1;

        UserSession::where('user_id', $userId)
            ->increment('risk_score', $increment);
    }

    protected function triggerHighRiskAlert($userId, $riskScore, $eventCounts)
    {
        // Log high-risk activity
        Log::warning('High risk user activity detected', [
            'user_id' => $userId,
            'risk_score' => $riskScore,
            'event_counts' => $eventCounts,
            'timestamp' => now()
        ]);

        // Notify security team (implement your notification system)
        // $this->notifySecurityTeam($userId, $riskScore, $eventCounts);

        // Consider additional actions:
        // - Force re-authentication
        // - Temporarily suspend account
        // - Increase monitoring level
    }

    protected function logSuspiciousActivity($userId, $activity, $details = [])
    {
        SecurityAuditLog::create([
            'user_id' => $userId,
            'event' => 'SUSPICIOUS_ACTIVITY',
            'details' => array_merge(['activity' => $activity], $details),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);
    }
}
