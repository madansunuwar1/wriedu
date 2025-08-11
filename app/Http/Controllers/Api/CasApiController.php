<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CASFeedback;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CASFeedbackNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CasApiController extends Controller
{
    /**
     * Get CAS feedback for a specific application.
     */
    public function index($applicationId)
    {
        try {
            $casFeedbacks = CASFeedback::with('user:id,name,last,avatar') // Eager load user with selected fields
                ->where('application_id', $applicationId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $casFeedbacks
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch CAS feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch CAS feedback.'
            ], 500);
        }
    }

    /**
     * Store a newly created CAS feedback in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:applications,id',
            'feedback_type' => 'nullable|string|max:255',
            'priority' => 'nullable|in:Low,Medium,High,Critical',
            'subject' => 'required|string|max:255',
            'feedback' => 'required|string',
            'status' => 'nullable|in:Open,In Progress,Resolved,Closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $casFeedback = CASFeedback::create([
                'application_id' => $request->application_id,
                'user_id' => Auth::id(),
                'feedback_type' => $request->feedback_type ?? 'General',
                'priority' => $request->priority ?? 'Medium',
                'subject' => $request->subject,
                'feedback' => $request->feedback,
                'status' => $request->status ?? 'Open',
                'entry_type' => 'cas_feedback',
            ]);

            Log::info('CAS feedback created via API: ID ' . $casFeedback->id);

            // --- Email Notification Logic (copied from your original controller) ---
            $application = Application::with(['assignedUser', 'createdBy'])->find($request->application_id);
            if ($application) {
                $emailsToSend = [];
                if ($application->email) $emailsToSend['student'] = $application->email;
                if ($application->partnerDetails) {
                    try {
                        $partner = \App\Models\Partner::find($application->partnerDetails);
                        if ($partner && $partner->email) $emailsToSend['partner'] = $partner->email;
                    } catch (\Exception $e) {
                        Log::warning("Failed to load partner details for email: " . $e->getMessage());
                    }
                }
                if ($application->createdBy && $application->createdBy->email) $emailsToSend['creator'] = $application->createdBy->email;
                
                // Remove duplicates before sending
                $uniqueEmails = array_unique($emailsToSend);

                foreach ($uniqueEmails as $recipientType => $email) {
                    try {
                        Mail::to($email)->send(new CASFeedbackNotification($casFeedback, $application, $recipientType));
                        Log::info("CAS feedback email sent to $recipientType at: $email");
                    } catch (\Exception $e) {
                        Log::error("Failed to send CAS feedback email to $recipientType: " . $e->getMessage());
                    }
                }
            }
            // --- End Email Logic ---
            
            // Load user relationship for the response
            $casFeedback->load('user:id,name,last,avatar');

            return response()->json([
                'success' => true, 
                'message' => 'CAS Feedback submitted successfully!',
                'data' => $casFeedback
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('CAS Feedback submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to submit CAS feedback.'
            ], 500);
        }
    }

    /**
     * Update the specified CAS feedback in storage.
     */
    public function update(Request $request, CASFeedback $casFeedback)
    {
        // Check if user can edit this feedback (owner or admin)
        if ($casFeedback->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
             return response()->json(['success' => false, 'message' => 'You are not authorized to edit this feedback.'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'feedback_type' => 'required|string|max:255',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'subject' => 'required|string|max:255',
            'feedback' => 'required|string',
            'status' => 'required|in:Open,In Progress,Resolved,Closed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $casFeedback->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'CAS Feedback updated successfully!',
                'data' => $casFeedback
            ]);
        } catch (\Exception $e) {
             Log::error('CAS Feedback update failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update CAS feedback.'], 500);
        }
    }

    /**
     * Remove the specified CAS feedback from storage.
     */
    public function destroy(CASFeedback $casFeedback)
    {
        // Check if user can delete this feedback (owner or admin)
        if ($casFeedback->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to delete this feedback.'], 403);
        }

        try {
            $casFeedback->delete();
            return response()->json(['success' => true, 'message' => 'CAS Feedback deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('CAS Feedback deletion failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete CAS feedback.'], 500);
        }
    }
}