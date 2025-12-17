<?php

namespace App\Http\Controllers;

use App\Models\RoommateProfile;
use App\Models\User;
use App\Services\CompatibilityService;
use Illuminate\Http\Request;

class RoommateProfileController extends Controller
{
    protected $compatibilityService;

    public function __construct(CompatibilityService $compatibilityService)
    {
        $this->compatibilityService = $compatibilityService;
    }
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
            // New compatibility fields
            'pref_no_smoker'               => 'boolean',
            'pref_pets_ok'                 => 'boolean',
            'pref_same_gender_only'        => 'boolean',
            'pref_visitors_ok'             => 'boolean',
            'pref_substance_free_required' => 'boolean',
            'uses_substances'              => 'boolean',
            'cleanliness'                  => 'nullable|integer|min:1|max:5',
            'noise_tolerance'              => 'nullable|integer|min:1|max:5',
            'sleep_schedule'               => 'nullable|integer|min:1|max:5',
            'study_focus'                  => 'nullable|integer|min:1|max:5',
            'social_level'                 => 'nullable|integer|min:1|max:5',
            'schedule_type'                => 'nullable|in:morning,night,mixed',
            'occupation_field'             => 'nullable|string|max:255',
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

    // Show a specific roommate profile
    public function show(RoommateProfile $roommateProfile)
    {
        $roommateProfile->load('user');
        
        // Calculate compatibility if user is logged in and not viewing themselves
        $compatibility = null;
        if (auth()->check() && auth()->user()->roommateProfile && auth()->id() !== $roommateProfile->user_id) {
            $compatibility = $this->compatibilityService->calculate(
                auth()->user()->roommateProfile, 
                $roommateProfile
            );
        }

        return view('roommates.show', compact('roommateProfile', 'compatibility'));
    }

    // API endpoint for compatibility (if needed for dynamic updates, though we passed it in show)
    public function compatibility(User $user)
    {
        // $user is the target user
        $targetProfile = $user->roommateProfile;
        $viewerProfile = auth()->user()->roommateProfile;

        if (!$targetProfile || !$viewerProfile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $result = $this->compatibilityService->calculate($viewerProfile, $targetProfile);

        return response()->json($result);
    }
}
