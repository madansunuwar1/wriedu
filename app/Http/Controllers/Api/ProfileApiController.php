<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileApiController extends Controller
{
    /**
     * Display the authenticated user's profile data.
     * This replaces the old `edit()` method.
     *
     * @param Request $request
     * @return JsonResponse
     */
     public function show(Request $request, User $userId = null): JsonResponse
    {
        // If a $userId is passed from the route (e.g., /api/user/profile/5), use it.
        // Otherwise, fall back to the authenticated user.
        // This uses Route Model Binding, which is cleaner than User::findOrFail().
        // Ensure your route is defined as '/user/profile/{userId}' for this to work.
        // We'll update it to {user} to be conventional
        $profileUser = $userId ?? $request->user();

        // The authorization is now handled in the routes/api.php file, so no check is needed here.

        return response()->json($profileUser);
    }
    /**
     * Update the user's profile information.
     * This replaces the old `update()` method.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request, $userId = null): JsonResponse
    {
        $userToUpdate = null;
        $currentUser = $request->user();

        if ($userId) {
            $userToUpdate = User::findOrFail($userId);
        } else {
            $userToUpdate = $currentUser;
        }

        // --- THIS IS THE FIX ---
        // We replace the policy check with a direct, manual authorization check.

        // OLD CODE TO REMOVE:
        // $this->authorize('update', $userToUpdate);

        // NEW CODE TO ADD:
        // Rule 1: Allow if the current user is an Administrator.
        // Rule 2: Allow if the user is updating their own profile.
         if (!$currentUser->hasRole('Administrator') && $currentUser->id !== $userToUpdate->id) {
        // If the user does NOT have the 'Administrator' role AND they are NOT updating themselves...
        abort(403, 'This action is unauthorized.');
    }
        // --- END OF FIX ---


        // The rest of your method stays exactly the same.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'last' => 'sometimes|string|max:255|nullable',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userToUpdate->id),
            ],
        ]);

        if ($userToUpdate->email !== $validatedData['email'] && $userToUpdate instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            $validatedData['email_verified_at'] = null;
        }

        $userToUpdate->fill($validatedData);
        $userToUpdate->save();

        return response()->json($userToUpdate);
    }

    /**
     * Update the user's password.
     * This is a dedicated method for the password change form.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
    }


    /**
     * Delete the user's account.
     * This replaces the old `destroy()` method.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        // Validate the password
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // For APIs (using Sanctum), we revoke the token.
        // The Vue app will then handle the "logout" by deleting the
        // token from local storage and redirecting.
        $user->currentAccessToken()->delete();

        $user->delete();

        // Return a success message.
        return response()->json(['message' => 'Your account has been successfully deleted.']);
    }
}
