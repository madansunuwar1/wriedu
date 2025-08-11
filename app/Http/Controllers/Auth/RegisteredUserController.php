<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use App\Models\Role;
use App\Models\Lead;
use App\Jobs\ProcessNewUserNotifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class RegisteredUserController extends Controller
{


    public function __construct()
    {
        // Apply the 'auth' middleware to all methods except 'store'
        $this->middleware('auth')->except(['store', 'create']);

        // Apply the 'can:view_users' middleware to all methods except 'store'
        $this->middleware('can:view_users')->except(['store', 'create']);
    }


    public function index(): View
    {
    if (Gate::denies('view_users')) {
        \Log::warning('Unauthorized attempt to view leads by user: ' . auth()->user()->id);
        abort(403, 'Unauthorized action.');
    }
    $leads = Lead::all();
    $roles = Role::all();
    
    // Get users with basic information and eager load roles
    $users = User::select('id', 'name', 'last', 'email', 'application')
        ->with('roles')  // Eager load roles
        ->orderBy('id')
        ->paginate(25);
    
    // Add role property to each user object
    foreach ($users as $user) {
        // Get the first role name or null
        $user->role = $user->roles->first() ? $user->roles->first()->name : null;
        
        // Handle application property
        if (is_string($user->application)) {
            $user->application = $user->application !== 'N/A' ? 
                explode(',', $user->application) : [];
        } elseif (!is_array($user->application)) {
            $user->application = [];
        }
        
        // Get leads assigned to this user
        $user->lead = Lead::where('user_id', $user->id)->pluck('id')->toArray();
    }

    return view('backend.user.index', compact('users', 'roles', 'leads'));
}

    public function profile(): View
    {
        $user = Auth::user(); 
        return view('backend.user.profile', compact('user'));
    }

    public function create(): View
    {
        $users = User::select('id', 'name', 'last', 'email')
            ->orderBy('id')
            ->get();
            
        return view('auth.register', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
           
            'gender' => 'required|in:male,female', // Validate gender input
 
            'applications' => ['sometimes', 'array'],
            'applications.*' => ['string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create user immediately
        $user = User::create([
            'name' => $validated['name'],
            'last' => $validated['last'],
            'email' => $validated['email'],
            'application' => implode(',', $request->input('applications', ['N/A'])),
           
          'gender' => $request->input('gender'),  
            'password' => Hash::make($validated['password']),
        ]);

        // Login immediately
        Auth::login($user);

        // Process notifications and events in background
        dispatch(function() use ($user) {
            event(new Registered($user));
            ProcessNewUserNotifications::dispatch($user)
                ->onQueue('emails-high');
        })->afterResponse();

        // Redirect immediately to verification page
        return redirect(route('verification.notice', absolute: false));
    }

    public function edit($id): View
    {
        $users = User::select('id', 'name', 'last', 'email')
            ->findOrFail($id);
            
        return view('backend.user.update', compact('users'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $users = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $id],
        ]);

        $users->update($validated);

        return redirect()->route('backend.user.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('backend.user.index')
            ->with('success', 'User deleted successfully');
    }
   public function updateAssignments(Request $request, $id)
    {
        try {
            DB::beginTransaction();
    
            $user = User::findOrFail($id);
            $newApplicationIds = $request->application ?? [];
            $newLeadIds = $request->lead ?? [];
    
            // Determine which type of assignment we're updating (application or lead)
            $isApplicationUpdate = !empty($newApplicationIds);
            $isLeadUpdate = !empty($newLeadIds);
    
            // Make sure we're only updating one type at a time
            if ($isApplicationUpdate && $isLeadUpdate) {
                throw new \Exception('Cannot update both applications and leads simultaneously');
            }
    
            if ($isApplicationUpdate) {
                // Validate the application IDs
                $validUsers = User::whereIn('id', $newApplicationIds)->count();
    
                if ($validUsers !== count($newApplicationIds)) {
                    throw new \Exception('One or more invalid user IDs provided');
                }
    
                // Update the user's application field
                $user->application = $newApplicationIds;
                $user->save();
    
                // First, remove all existing assignments for this user
                Application::where('user_id', $id)->update(['user_id' => null]);
    
                // Assign new applications
                foreach ($newApplicationIds as $appId) {
                    // Assign applications created by the selected users to this user
                    Application::where('created_by', $appId)
                              ->update(['user_id' => $id]);
                }
    
                $message = 'Application assignments updated successfully';
            } 
            elseif ($isLeadUpdate) {
                // Validate the lead user IDs
                $validUsers = User::whereIn('id', $newLeadIds)->count();
    
                if ($validUsers !== count($newLeadIds)) {
                    throw new \Exception('One or more invalid user IDs provided');
                }
    
                // First, remove all existing assignments for this user
                Lead::where('user_id', $id)->update(['user_id' => null]);
    
                // Assign leads created by the selected users to this user
                Lead::whereIn('created_by', $newLeadIds)
                    ->update(['user_id' => $id]);
    
                $message = 'Lead assignments updated successfully';
            }
            else {
                // No assignments provided, clear both
                Application::where('user_id', $id)->update(['user_id' => null]);
                Lead::where('user_id', $id)->update(['user_id' => null]);
                
                $message = 'All assignments cleared successfully';
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating assignments: ' . $e->getMessage()
            ], 500);
        }
    }


// Add this method to restore soft-deleted users
public function restore($id)
{
    try {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        
        return redirect()->back()->with('success', 'User restored successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error restoring user: ' . $e->getMessage());
    }
}
}