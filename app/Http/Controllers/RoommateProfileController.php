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
        $query = RoommateProfile::query()->with('user');

        // Filter by city
        if ($request->filled('city')) {
            $query->where('preferred_city', 'like', '%' . $request->input('city') . '%');
        }

        // Budget range logic
        if ($request->filled('budget_range')) {
            switch ($request->input('budget_range')) {
                case 'low': // 10k - 25k
                    $query->where('budget_max', '>=', 10000)->where('budget_min', '<=', 25000);
                    break;
                case 'medium': // 25k - 50k
                    $query->where('budget_max', '>=', 25000)->where('budget_min', '<=', 50000);
                    break;
                case 'high': // 50k+
                    $query->where('budget_max', '>=', 50000);
                    break;
            }
        }

        // Pets / smoking
        if ($request->boolean('has_pets')) {
            $query->where('has_pets', 1);
        }

        if ($request->boolean('is_smoker')) {
            $query->where('is_smoker', 1);
        }

        // Get all matching profiles first (limit to reasonable max if needed, e.g. 200)
        $profilesCollection = $query->get();

        // Calculate compatibility & Filter
        $viewerProfile = auth()->user()->roommateProfile ?? null;
        
        $profilesCollection = $profilesCollection->map(function ($profile) use ($viewerProfile) {
            // Skip self score
            if (auth()->id() === $profile->user_id) {
                $profile->compatibility_score = null;
                return $profile;
            }

            if ($viewerProfile) {
                $result = $this->compatibilityService->calculate($viewerProfile, $profile);
                $profile->compatibility_score = $result['score'];
                $profile->compatibility_data = $result;
            } else {
                // Heuristic for guests
                $base = 70;
                if ($profile->preferred_city) $base += 5;
                $profile->compatibility_score = $base; // Simplified
            }
            return $profile;
        });

        // Filter by Compatibility
        if ($request->filled('min_compatibility')) {
            $minScore = (int) $request->input('min_compatibility');
            $profilesCollection = $profilesCollection->filter(function ($profile) use ($minScore) {
                // If no score (e.g. self), keep it? Or hide? Let's hide self in matches usually.
                // But for now, if score is null, filter out if strict.
                return $profile->compatibility_score >= $minScore;
            });
        }
        
        // Remove self from matches list generally?
        if (auth()->check()) {
            $profilesCollection = $profilesCollection->filter(function ($profile) {
                return $profile->user_id !== auth()->id();
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'best_match');
        switch ($sortBy) {
            case 'budget_low':
                $profilesCollection = $profilesCollection->sortBy(function ($profile) {
                    return (int) $profile->budget_max;
                });
                break;
            case 'budget_high':
                $profilesCollection = $profilesCollection->sortByDesc(function ($profile) {
                    return (int) $profile->budget_max;
                });
                break;
            case 'newest':
                $profilesCollection = $profilesCollection->sortByDesc('created_at');
                break;
            case 'best_match':
            default:
                // Sort by compatibility score, then by created_at as tie breaker
                $profilesCollection = $profilesCollection->sortByDesc(function ($profile) {
                    return [(int) $profile->compatibility_score, $profile->created_at];
                });
                break;
        }

        // Manual Pagination
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 9;
        $items = $profilesCollection->values()->forPage($page, $perPage);
        
        $profiles = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $profilesCollection->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

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
        // Sanitize boolean fields before validation
        $booleans = [
            'is_smoker', 'has_pets', 
            'pref_no_smoker', 'pref_pets_ok', 'pref_same_gender_only', 
            'pref_visitors_ok', 'pref_substance_free_required', 'uses_substances'
        ];

        foreach ($booleans as $field) {
            $request->merge([$field => $request->boolean($field) ? 1 : 0]);
        }

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
        // Booleans are already sanitized and included in $data by validate() because of merge()

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
