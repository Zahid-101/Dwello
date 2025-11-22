<?php

namespace App\Http\Controllers;

use App\Models\RoommateProfile;
use Illuminate\Http\Request;

class RoommateProfileController extends Controller
{
    // List all roommate profiles
    public function index()
    {
        $profiles = RoommateProfile::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('roommates.index', compact('profiles'));
    }

    // Show form to create / update current user's profile
    public function create()
    {
        $profile = auth()->user()->roommateProfile;

        return view('roommates.create', compact('profile'));
    }

    // Save or update the roommate profile
    public function store(Request $request)
    {
        $data = $request->validate([
            'display_name'        => 'required|string|max:255',
            'age'                 => 'nullable|integer|min:16|max:100',
            'gender'              => 'nullable|in:male,female,other',
            'budget_min'          => 'nullable|numeric|min:0',
            'budget_max'          => 'nullable|numeric|min:0',
            'preferred_city'      => 'nullable|string|max:255',
            'preferred_location'  => 'nullable|string|max:255',
            'move_in_date'        => 'nullable|date',
            'is_smoker'           => 'nullable|boolean',
            'has_pets'            => 'nullable|boolean',
            'bio'                 => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        $data['is_smoker'] = $request->has('is_smoker');
        $data['has_pets']  = $request->has('has_pets');

        // Create or update the user's profile
        RoommateProfile::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return redirect()
            ->route('roommates.index')
            ->with('success', 'Roommate profile saved!');
    }
}