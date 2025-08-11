<?php

namespace App\Http\Controllers;

use App\Models\User;  // Make sure to import the User model
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function showGuestPage()
    {
        // Fetch users from the database and paginate
        $users = User::select('id', 'name', 'last', 'email', 'application')
            ->orderBy('id')
            ->paginate(25);

        // Pass the users to the view using compact
        return view('guest', compact('users'));
    }
}
