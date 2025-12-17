<?php

namespace App\Services;

use App\Models\RoommateProfile;

class CompatibilityService
{
    /**
     * Calculate compatibility between two profiles.
     *
     * @param RoommateProfile $viewer
     * @param RoommateProfile $target
     * @return array
     */
    public function calculate(RoommateProfile $viewer, RoommateProfile $target): array
    {
        $conflicts = $this->checkDealBreakers($viewer, $target);

        if (!empty($conflicts)) {
            return [
                'score' => 0,
                'conflicts' => $conflicts,
                'reasons' => [],
                'breakdown' => [],
            ];
        }

        return $this->calculateWeightedScore($viewer, $target);
    }

    /**
     * Check for hard conflicts (deal-breakers).
     */
    private function checkDealBreakers(RoommateProfile $viewer, RoommateProfile $target): array
    {
        $conflicts = [];

        // 1. Smoking
        if ($viewer->pref_no_smoker && $target->is_smoker) {
            $conflicts[] = "Non-smoker required";
        }

        // 2. Gender
        if ($viewer->pref_same_gender_only) {
            if ($viewer->gender && $target->gender && $viewer->gender !== $target->gender) {
                // Only conflict if both genders are known and different
                $conflicts[] = "Same gender only";
            }
        }

        // 3. Substances
        if ($viewer->pref_substance_free_required && $target->uses_substances) {
            $conflicts[] = "Substance-free living required";
        }

        // 4. Pets
        // If viewer pref_pets_ok is false (0), and target has pets (1)
        if (!$viewer->pref_pets_ok && $target->has_pets) {
            $conflicts[] = "No pets preferred";
        }

        return $conflicts;
    }

    /**
     * Calculate weighted score based on lifestyle scales and background.
     */
    private function calculateWeightedScore(RoommateProfile $viewer, RoommateProfile $target): array
    {
        $weights = [
            'cleanliness' => 0.25,
            'noise_tolerance' => 0.20,
            'sleep_schedule' => 0.20,
            'study_focus' => 0.15,
            'social_level' => 0.10,
            'occupation_field' => 0.10,
        ];

        $totalWeight = 0;
        $weightedSum = 0;
        $breakdown = [];
        $reasons = [];

        // Process scalar attributes (1-5 scales)
        $scales = ['cleanliness', 'noise_tolerance', 'sleep_schedule', 'study_focus', 'social_level'];

        foreach ($scales as $attribute) {
            $valA = $viewer->$attribute;
            $valB = $target->$attribute;

            if (!is_null($valA) && !is_null($valB)) {
                $diff = abs($valA - $valB);
                // Score 0..1 (0=worst, 1=best). Max diff is 4.
                // Formula: 1 - (diff / 4)
                $subScore = max(0, 1 - ($diff / 4));
                
                $weight = $weights[$attribute];
                $weightedSum += $subScore * $weight;
                $totalWeight += $weight;

                $breakdown[$attribute] = round($subScore * 100);

                // Add reasons
                $label = ucfirst(str_replace('_', ' ', $attribute));
                if ($diff <= 1) {
                    $reasons[] = ['type' => 'positive', 'text' => "Similar $label"];
                } elseif ($diff >= 3) {
                    $reasons[] = ['type' => 'warning', 'text' => "Different $label preferences"];
                }
            }
        }

        // Process Occupation (String match)
        if ($viewer->occupation_field && $target->occupation_field) {
            $weight = $weights['occupation_field'];
            $totalWeight += $weight;

            // Simple case-insensitive match
            $match = strcasecmp($viewer->occupation_field, $target->occupation_field) === 0;
            $subScore = $match ? 1.0 : 0.0; // Binary 0 or 1 for string match? 
            // Or maybe partial? Instructions say "occupation_field match". Let's Stick to exact match for simplicity or maybe similar text?
            // "If occupation_field matches -> Similar occupation background" implies logic.
            // Let's stick to exact match for score, but maybe loose for reasons? No, instructions are specific.
            
            $weightedSum += $subScore * $weight;
            $breakdown['occupation_field'] = $match ? 100 : 0;

            if ($match) {
                $reasons[] = ['type' => 'positive', 'text' => "Similar occupation background"];
            }
        }

        if ($totalWeight == 0) {
            return [
                'score' => null,
                'conflicts' => [],
                'reasons' => [],
                'message' => "Not enough data to calculate compatibility",
            ];
        }

        // Normalize score to 0-100
        $finalScore = ($weightedSum / $totalWeight) * 100;

        // Filter reasons: max 3 positives, max 2 warnings, total max 5
        $positives = array_filter($reasons, fn($r) => $r['type'] === 'positive');
        $warnings = array_filter($reasons, fn($r) => $r['type'] === 'warning');

        $finalReasons = [];
        $pCount = 0;
        foreach ($positives as $p) {
            if ($pCount < 3) {
                $finalReasons[] = $p;
                $pCount++;
            }
        }
        $wCount = 0;
        foreach ($warnings as $w) {
            // Only add warnings if we have space (max 5 total) but strict limit 2 warnings? 
            // "up to 3 positives + up to 2 warnings"
            if ($wCount < 2) {
                $finalReasons[] = $w;
                $wCount++;
            }
        }

        return [
            'score' => round($finalScore),
            'conflicts' => [],
            'reasons' => $finalReasons,
            'breakdown' => $breakdown,
        ];
    }
}
