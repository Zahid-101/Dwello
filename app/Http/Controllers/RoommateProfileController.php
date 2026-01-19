<?php

namespace App\Http\Controllers;

use App\Models\RoommateProfile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Services\CompatibilityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
                if ($profile->preferred_city)
                    $base += 5;
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
            $userId = auth()->id();
            $rejectedIds = auth()->user()->rejectedUsers()->pluck('rejected_user_id')->toArray();
            
            $profilesCollection = $profilesCollection->filter(function ($profile) use ($userId, $rejectedIds) {
                return $profile->user_id !== $userId && !in_array($profile->user_id, $rejectedIds);
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
                // Deprioritize "dealbreakers" (score 0) - though we might just filter them out if stricter
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

        // Fetch matching properties for the "Comparisons/Matches" tab
        $matchingPropertiesParams = collect(); // Default empty

        if (auth()->check() && auth()->user()->roommateProfile) {
            $myProfile = auth()->user()->roommateProfile;

            $propQuery = \App\Models\Property::with('photos');

            // Filter by City
            if ($myProfile->preferred_city) {
                $propQuery->where('city', $myProfile->preferred_city);
            }

            // Filter by Budget (Property rent <= User max budget)
            if ($myProfile->budget_max) {
                $propQuery->where('monthly_rent', '<=', $myProfile->budget_max);
            }

            // Filter by Property Type if set
            if ($myProfile->preferred_property_type) {
                $propQuery->where('property_type', $myProfile->preferred_property_type);
            }

            $matchingPropertiesParams = $propQuery->inRandomOrder()->limit(6)->get();

            // Fallback: if strict match yields few results, relax constraints
            if ($matchingPropertiesParams->count() < 3) {
                $ids = $matchingPropertiesParams->pluck('id');
                $relaxedQuery = \App\Models\Property::with('photos')->whereNotIn('id', $ids);
                // Relaxed: Match city OR budget
                $relaxedQuery->where(function ($q) use ($myProfile) {
                    if ($myProfile->preferred_city)
                        $q->where('city', $myProfile->preferred_city);
                    if ($myProfile->budget_max)
                        $q->orWhere('monthly_rent', '<=', $myProfile->budget_max);
                });
                $more = $relaxedQuery->inRandomOrder()->limit(6 - $matchingPropertiesParams->count())->get();
                $matchingPropertiesParams = $matchingPropertiesParams->merge($more);
            }
        }

        return view('roommates.index', compact('profiles', 'matchingPropertiesParams'));
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
            'is_smoker',
            'has_pets',
            'pref_no_smoker',
            'pref_pets_ok',
            'pref_same_gender_only',
            'pref_visitors_ok',
            'pref_substance_free_required',
            'uses_substances'
        ];

        foreach ($booleans as $field) {
            $request->merge([$field => $request->boolean($field) ? 1 : 0]);
        }

        $request->validate([
            'profile_photo' => 'nullable|image|max:1024',
        ]);

        $user = auth()->user();
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        $data = $request->validate([
            'display_name' => 'required|string|max:50',
            'age' => 'nullable|integer|min:16|max:100',
            'gender' => 'nullable|in:male,female,other',
            'budget_min' => 'nullable|numeric|min:0|max:10000000',
            'budget_max' => 'nullable|numeric|min:0|max:10000000|gte:budget_min', // Max >= Min
            'preferred_city' => ['nullable', 'string', 'max:50', Rule::in(config('cities'))],
            'preferred_property_type' => 'nullable|in:room,apartment,house',
            'preferred_location' => 'nullable|string|max:255',
            'move_in_date' => 'nullable|date|after_or_equal:today',
            'is_smoker' => 'nullable|boolean',
            'has_pets' => 'nullable|boolean',
            'bio' => 'nullable|string|max:1000',
            // New compatibility fields
            'pref_no_smoker' => 'boolean',
            'pref_pets_ok' => 'boolean',
            'pref_same_gender_only' => 'boolean',
            'pref_visitors_ok' => 'boolean',
            'pref_substance_free_required' => 'boolean',
            'uses_substances' => 'boolean',
            'noise_tolerance' => 'nullable|integer|min:1|max:5',
            'sleep_schedule' => 'nullable|integer|min:1|max:5',
            'study_focus' => 'nullable|integer|min:1|max:5',
            'social_level' => 'nullable|integer|min:1|max:5',
            'schedule_type' => 'nullable|in:morning,night,mixed',
            'occupation_field' => 'nullable|string|max:50',
        ], [
            'budget_max.gte' => 'Maximum budget must be greater than or equal to minimum budget.',
            'move_in_date.after_or_equal' => 'Move-in date cannot be in the past.',
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

        $compatibility = null;
        $matchingProperties = collect();

        if (auth()->check() && auth()->user()->roommateProfile && auth()->id() !== $roommateProfile->user_id) {
            $viewerProfile = auth()->user()->roommateProfile;

            // Calculate Compatibility
            $compatibility = $this->compatibilityService->calculate(
                $viewerProfile,
                $roommateProfile
            );

            // Cascading Logic to GUARANTEE results if properties exist
            $city = $roommateProfile->preferred_city ?? $viewerProfile->preferred_city;
            $type = $roommateProfile->preferred_property_type ?? $viewerProfile->preferred_property_type;
            $combinedBudget = ($viewerProfile->budget_max ?? 0) + ($roommateProfile->budget_max ?? 0);

            // 1. Strict Match: City + Type + Budget
            $query = \App\Models\Property::with('photos');
            if ($city)
                $query->where('city', $city);
            if ($type)
                $query->where('property_type', $type);
            if ($combinedBudget > 0)
                $query->where('monthly_rent', '<=', $combinedBudget);

            $matchingProperties = $query->inRandomOrder()->limit(3)->get();

            // 2. Relaxed Match: City + Budget (Ignore Type)
            if ($matchingProperties->count() < 3) {
                $needed = 3 - $matchingProperties->count();
                $ids = $matchingProperties->pluck('id');

                $query = \App\Models\Property::with('photos')->whereNotIn('id', $ids);
                if ($city)
                    $query->where('city', $city);
                if ($combinedBudget > 0)
                    $query->where('monthly_rent', '<=', $combinedBudget);

                $more = $query->inRandomOrder()->limit($needed)->get();
                $matchingProperties = $matchingProperties->merge($more);
            }

            // 3. Relaxed Match: City only (Ignore Budget)
            if ($matchingProperties->count() < 3) {
                $needed = 3 - $matchingProperties->count();
                $ids = $matchingProperties->pluck('id');

                $query = \App\Models\Property::with('photos')->whereNotIn('id', $ids);
                if ($city)
                    $query->where('city', $city);

                $more = $query->inRandomOrder()->limit($needed)->get();
                $matchingProperties = $matchingProperties->merge($more);
            }

            // 4. Default: Any Property (Ignore City, show anything)
            if ($matchingProperties->count() < 3) {
                $needed = 3 - $matchingProperties->count();
                $ids = $matchingProperties->pluck('id');

                $query = \App\Models\Property::with('photos')->whereNotIn('id', $ids);
                $more = $query->inRandomOrder()->limit($needed)->get();
                $matchingProperties = $matchingProperties->merge($more);
            }
        }

        return view('roommates.show', compact('roommateProfile', 'compatibility', 'matchingProperties'));
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
    
    public function reject(User $user)
    {
        if (auth()->id() === $user->id) {
             return response()->json(['error' => 'Cannot reject yourself'], 400);
        }
        
        auth()->user()->rejectedUsers()->syncWithoutDetaching([$user->id]);
        
        return response()->json(['success' => true]);
    }
}
