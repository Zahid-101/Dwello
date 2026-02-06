<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleSelectionController extends Controller
{
    /**
     * Show the role selection screen.
     */
    public function show()
    {
        // If user already has a role, redirect to dashboard or home
        if (auth()->user()->role) {
            return redirect()->route('home');
        }

        return view('auth.select-role');
    }

    /**
     * Store the selected role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:landlord,seeker',
        ]);

        $user = auth()->user();

        // Prevent changing role if already set (security)
        if ($user->role) {
            return redirect()->route('home');
        }

        $user->update([
            'role' => $request->input('role')
        ]);

        // Redirect based on role
        // Redirect to homepage for all roles as requested
        return redirect()->route('home')
            ->with('success', 'Welcome! Your role has been set.')
            ->with('show_onboarding_popup', true);
    }
}
