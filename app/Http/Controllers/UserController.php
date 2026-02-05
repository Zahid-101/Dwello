<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display the specified user (public profile).
     */
    public function show(User $user)
    {
        // Ideally only show landlords or seekers, but let's be open for now.
        // Maybe check if they are a landlord if we only want landlord profiles:
        // if (!$user->isLandlord()) abort(404);

        $user->load(['properties.photos', 'roommateProfile']);

        $authUser = auth()->user();
        $compatibility = null;
        $matchingProperties = collect();

        // 1. Calculate Compatibility (if both are Roommate Seekers with profiles)
        if ($authUser && $authUser->id !== $user->id && $authUser->roommateProfile && $user->roommateProfile) {
            $score = 0;
            $maxScore = 0;

            $myProfile = $authUser->roommateProfile;
            $theirProfile = $user->roommateProfile;

            // Budget Overlap (30 points)
            // If ranges overlap at all, give full points? Or simpler: if their budget fits mine.
            // Let's check overlap.
            $maxLower = max($myProfile->budget_min, $theirProfile->budget_min);
            $minUpper = min($myProfile->budget_max, $theirProfile->budget_max);
            if ($minUpper >= $maxLower) {
                $score += 30;
            }
            $maxScore += 30;

            // Location Match (20 points)
            if (strcasecmp($myProfile->preferred_city, $theirProfile->preferred_city) === 0) {
                $score += 20;
            }
            $maxScore += 20;

            // Smoking Preference (15 points) - Dealbreaker check
            // If I smoke and they say no smoker: 0 points.
            // If I don't smoke, full points.
            $smokingConflict = ($myProfile->is_smoker && $theirProfile->pref_no_smoker) ||
                ($theirProfile->is_smoker && $myProfile->pref_no_smoker);

            if (!$smokingConflict) {
                $score += 15;
            }
            $maxScore += 15;

            // Lifestyle Logic (Similarity in 1-5 scales) (35 points)
            // Cleanliness (10), Sleep (10), Noise (15)
            // Diff 0 -> 100%, Diff 1 -> 75%, Diff 2 -> 50%, Diff 3 -> 25%, Diff 4 -> 0%
            $calcSim = function ($val1, $val2, $weight) {
                $diff = abs($val1 - $val2);
                if ($diff == 0)
                    return $weight;
                if ($diff == 1)
                    return $weight * 0.75;
                if ($diff == 2)
                    return $weight * 0.5;
                if ($diff == 3)
                    return $weight * 0.25;
                return 0;
            };

            $score += $calcSim($myProfile->cleanliness, $theirProfile->cleanliness, 10);
            $score += $calcSim($myProfile->sleep_schedule, $theirProfile->sleep_schedule, 10);
            $score += $calcSim($myProfile->noise_tolerance, $theirProfile->noise_tolerance, 15);
            $maxScore += 35;

            $compatibility = round(($score / $maxScore) * 100);
        }

        // 2. Find Matching Properties for THIS user (The one being viewed)
        // If they are a seeker, what properties match their criteria?
        if ($user->roommateProfile) {
            $profile = $user->roommateProfile;
            $query = \App\Models\Property::query();

            // Filter by City
            if ($profile->preferred_city) {
                $query->where('city', 'LIKE', '%' . $profile->preferred_city . '%');
            }

            // Filter by Max Budget
            if ($profile->budget_max) {
                $query->where('monthly_rent', '<=', $profile->budget_max);
            }

            // Exclude their own properties (if any, unlikely for seeker but good practice)
            $query->where('user_id', '!=', $user->id);

            $matchingProperties = $query->with('photos')->limit(4)->get();
        }

        return view('users.show', compact('user', 'compatibility', 'matchingProperties'));
    }
}
