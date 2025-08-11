<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Lead;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Jobs\ProcessNewUserNotifications; // <-- Import the Job
use Illuminate\Auth\Events\Registered;    // <-- Import the Event

class RegisteredUserApiController extends Controller
{
    /**
     * Get all data needed for the user management page.
     */
    public function index(): JsonResponse
    {
        // Get all available roles
        $roles = Role::all();

        // Get all users, including soft-deleted ones, for assignment dropdowns
        $allUsersForAssignment = User::with('roles')->orderBy('name')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'last' => $user->last,
                'role' => $user->roles->first() ? $user->roles->first()->name : null,
            ];
        });

        // Get primary list of users with pagination
        $users = User::withTrashed()
            ->with('roles')
            ->orderBy('id')
            ->paginate(25)
            ->through(function ($user) {
                // Format application and lead assignments into arrays
                $application = is_string($user->application) && $user->application !== 'N/A' ? explode(',', $user->application) : [];
                $lead = Lead::where('user_id', $user->id)->pluck('id')->toArray();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'last' => $user->last,
                    'email' => $user->email,
                    'avatar' => $user->avatar ? asset('storage/avatars/' . $user->avatar) : asset('assets/images/profile/user-1.jpg'),
                    'role' => $user->roles->first() ? $user->roles->first()->name : null,
                    'application' => $application,
                    'lead' => $lead,
                    'deleted_at' => $user->deleted_at,
                ];
            });

        return response()->json([
            'users' => $users,
            'roles' => $roles,
            'allUsersForAssignment' => $allUsersForAssignment,
        ]);
    }

    /**
     * Store a newly created user in storage.
     * This method is adapted from your RegisteredUserController to work for the API.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'last' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'string', 'exists:roles,name'], // Validation for the role from Vue form
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'last' => $validated['last'],
                'email' => $validated['email'],
                'application' => 'N/A', // Set a default value as in your web controller
                'password' => Hash::make($validated['password']),
            ]);

            // Assign the role sent from the Vue component
            $role = Role::where('name', $validated['role'])->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            // Dispatch events and jobs just like in your web controller
            dispatch(function () use ($user) {
                event(new Registered($user));
                ProcessNewUserNotifications::dispatch($user)->onQueue('emails-high');
            })->afterResponse();

            // Return a success JSON response
            return response()->json([
                'success' => true,
                'message' => 'User created successfully!'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors specifically
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle all other errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a user's role.
     */
    public function updateRole(Request $request, User $user): JsonResponse
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);

        $user->syncRoles([$request->role]);

        return response()->json(['success' => true, 'message' => 'User role updated successfully.']);
    }

    /**
     * Update user's application and lead assignments.
     */
    public function updateAssignments(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'application' => 'sometimes|array',
            'application.*' => 'integer|exists:users,id',
            'lead' => 'sometimes|array',
            'lead.*' => 'integer|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            // Handle Application Assignments
            if ($request->has('application')) {
                $newAppAssignmentUserIds = $request->input('application', []);
                $user->application = implode(',', $newAppAssignmentUserIds);
                $user->save();

                // Detach all old applications from this user
                Application::where('user_id', $user->id)->update(['user_id' => null]);
                // Assign new applications (created by the selected users)
                if (!empty($newAppAssignmentUserIds)) {
                    Application::whereIn('created_by', $newAppAssignmentUserIds)->update(['user_id' => $user->id]);
                }
            }

            // Handle Lead Assignments
            if ($request->has('lead')) {
                $newLeadAssignmentUserIds = $request->input('lead', []);

                // Detach all old leads from this user
                Lead::where('user_id', $user->id)->update(['user_id' => null]);
                // Assign new leads (created by the selected users)
                if (!empty($newLeadAssignmentUserIds)) {
                    Lead::whereIn('created_by', $newLeadAssignmentUserIds)->update(['user_id' => $user->id]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Assignments updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating assignments: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Soft-delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deactivated successfully.']);
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore($id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return response()->json(['success' => true, 'message' => 'User restored successfully.']);
    }

    /**
     * Export users to CSV.
     */
    public function export()
    {
        $fileName = 'users_export.csv';
        $users = User::with('roles')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Name', 'Last Name', 'Email', 'Role', 'Status'];

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                $row['ID']          = $user->id;
                $row['Name']        = $user->name;
                $row['Last Name']   = $user->last;
                $row['Email']       = $user->email;
                $row['Role']        = $user->roles->first() ? $user->roles->first()->name : 'N/A';
                $row['Status']      = $user->deleted_at ? 'Inactive' : 'Active';

                fputcsv($file, array_values($row));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
