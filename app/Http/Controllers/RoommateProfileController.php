<?php

namespace App\Http\Controllers;

use App\Models\RoommateProfile;
use Illuminate\Http\Request;

class RoommateProfileController extends Controller
{
    // List all roommate profiles
    public function index(Request $request)
    {
        $query = RoommateProfile::query()->with('user')->orderByDesc('created_at');

        // Filter by city
        if ($request->filled('city')) {
            $query->where('preferred_city', 'like', '%' . $request->input('city') . '%');
        }

        // Budget range
        if ($request->filled('min_budget')) {
            $query->where('budget_max', '>=', (float) $request->input('min_budget'));
        }

        if ($request->filled('max_budget')) {
            $query->where('budget_min', '<=', (float) $request->input('max_budget'));
        }

        // Pets / smoking
        if ($request->boolean('has_pets')) {
            $query->where('has_pets', 1);
        }

        if ($request->boolean('is_smoker')) {
            $query->where('is_smoker', 1);
        }

        $profiles = $query->paginate(9)->withQueryString();

        return view('roommates.index', compact('profiles'));
    }

    // Show form to create / update current user's profile
    public function create()
    {
        $profile = auth()->user()->roommateProfile ?? null;

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
