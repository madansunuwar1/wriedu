<?php

namespace App\Http\Controllers;

use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the comments.
     */
    public function index()
    {
        $comments = DB::table('comments')
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->select('comments.*', 'users.name as author_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }

    /**
     * Store a newly created comment with mentions.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
            'mentioned_user_id' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $commentData = [
                'content' => $request->input('content'),
                'user_id' => Auth::id()
            ];
            
            $mentionedUserId = $request->input('mentioned_user_id');
            
            $comment = $this->commentService->addCommentWithMention($commentData, $mentionedUserId);
            
            return response()->json([
                'success' => true,
                'message' => 'Comment created successfully',
                'data' => [
                    'comment_id' => $comment
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified comment.
     */
    public function show($id)
    {
        try {
            $comment = DB::table('comments')
                ->leftJoin('users', 'comments.user_id', '=', 'users.id')
                ->leftJoin('comment_mentions', 'comments.id', '=', 'comment_mentions.comment_id')
                ->leftJoin('users as mentioned_users', 'comment_mentions.mentioned_user_id', '=', 'mentioned_users.id')
                ->where('comments.id', $id)
                ->select(
                    'comments.*',
                    'users.name as author_name',
                    'mentioned_users.name as mentioned_user_name'
                )
                ->first();

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add mention to an existing comment.
     */
    public function addMention(Request $request, $commentId)
    {
        $validator = Validator::make($request->all(), [
            'mentioned_user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mentionedUserId = $request->input('mentioned_user_id');
            
            $this->commentService->addMentionToExistingComment($commentId, $mentionedUserId);
            
            return response()->json([
                'success' => true,
                'message' => 'Mention added successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add mention',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updated = DB::table('comments')
                ->where('id', $id)
                ->where('user_id', Auth::id()) // Ensure user owns the comment
                ->update([
                    'content' => $request->input('content'),
                    'updated_at' => now()
                ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found or unauthorized'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified comment.
     */
    public function destroy($id)
    {
        try {
            $deleted = DB::table('comments')
                ->where('id', $id)
                ->where('user_id', Auth::id()) // Ensure user owns the comment
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found or unauthorized'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}