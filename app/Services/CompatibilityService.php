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
        // Base weights (sum = 1.0)
        // If data is missing, we re-normalize based on total weight of available fields.
        $baseWeights = [
            'noise_tolerance' => 0.25,
            'sleep_schedule' => 0.25,
            'study_focus' => 0.20,
            'social_level' => 0.20,
            'occupation_field' => 0.10,
        ];

        $totalWeight = 0;
        $weightedSum = 0;
        $breakdown = [];
        $reasons = [];

        // 1. Lifestyle Scales (1-5)
        $scales = ['noise_tolerance', 'sleep_schedule', 'study_focus', 'social_level'];

        foreach ($scales as $attribute) {
            $valA = $viewer->$attribute;
            $valB = $target->$attribute;

            if (!is_null($valA) && !is_null($valB)) {
                $diff = abs($valA - $valB);
                // Score formula: 1 - (diff / 4)
                $subScore = max(0, 1 - ($diff / 4));

                $weight = $baseWeights[$attribute];
                $weightedSum += $subScore * $weight;
                $totalWeight += $weight;

                $breakdown[$attribute] = round($subScore * 100);

                // Generate Reasons with Labels
                // We use the viewer's label for 'Similar' to give context: "Similar noise preference (Quiet)"
                // For conflict, show both: "Very different... (You: Quiet, Them: Loud)"
                $labelA = RoommateProfile::getLabel($attribute, $valA);
                $labelB = RoommateProfile::getLabel($attribute, $valB);

                // Friendly attribute name for display
                $attrName = str_replace('_', ' ', $attribute);
                // specialized phrasing
                if ($attribute === 'sleep_schedule')
                    $attrName = 'sleep routine';
                if ($attribute === 'noise_tolerance')
                    $attrName = 'noise preference';
                if ($attribute === 'study_focus')
                    $attrName = 'study habits';
                if ($attribute === 'social_level')
                    $attrName = 'social preference';

                if ($diff <= 1) {
                    $reasons[] = ['type' => 'positive', 'text' => "Similar $attrName ($labelA)"];
                } elseif ($diff >= 3) {
                    $reasons[] = ['type' => 'warning', 'text' => "Very different $attrName (You: $labelA, Them: $labelB)"];
                }
            }
        }

        // 2. Occupation Match (String)
        if ($viewer->occupation_field && $target->occupation_field) {
            $weight = $baseWeights['occupation_field'];
            $totalWeight += $weight;

            $match = strcasecmp($viewer->occupation_field, $target->occupation_field) === 0;
            $subScore = $match ? 1.0 : 0.0;

            $weightedSum += $subScore * $weight;
            $breakdown['occupation_field'] = $match ? 100 : 0;

            if ($match) {
                $reasons[] = ['type' => 'positive', 'text' => "Similar occupation background"];
            }
        }

        // 3. Property Type Match
        if ($viewer->preferred_property_type && $target->preferred_property_type) {
            // Give it a small weight bonus or factor it into existing weights?
            // Let's add it as a new factor.
            $weight = 0.15; // 15% weight
            $totalWeight += $weight;

            if ($viewer->preferred_property_type === $target->preferred_property_type) {
                $weightedSum += 1.0 * $weight;
                $breakdown['property_type'] = 100;
                $reasons[] = ['type' => 'positive', 'text' => "Both looking for a " . ucfirst($viewer->preferred_property_type)];
            } else {
                $breakdown['property_type'] = 0;
                $reasons[] = ['type' => 'warning', 'text' => "Looking for different property types ({$viewer->preferred_property_type} vs {$target->preferred_property_type})"];
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

        // ... filtering reasons remains similar ...

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
