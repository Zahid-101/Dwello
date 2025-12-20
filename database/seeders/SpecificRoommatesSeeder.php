<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\RoommateProfile;
use Carbon\Carbon;
use Faker\Factory as Faker;

class SpecificRoommatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $targetLocations = ['Maligawatta', 'Dehiwala', 'Wellawatte'];
        
        $genders = ['male', 'female'];
        $scheduleTypes = ['morning', 'night', 'mixed'];
        
        // Ensure we create enough profiles for testing matching
        for ($i = 0; $i < 20; $i++) {
            $location = $targetLocations[array_rand($targetLocations)];
            $gender = $genders[array_rand($genders)];
            
            // 1. Create User
            // Use a pattern so we don't collide if run multiple times, or just rely on random
            $email = 'profile_' . strtolower($location) . '_' . $i . '_' . rand(100,999) . '@example.com';
            
            // Check if user exists (skip if so)
            if (User::where('email', $email)->exists()) continue;
            
            $user = User::create([
                'name' => $faker->name($gender),
                'email' => $email,
                'password' => Hash::make('password'),
            ]);

            // 2. Create Profile
            RoommateProfile::create([
                'user_id' => $user->id,
                'display_name' => $user->name,
                'age' => rand(20, 35),
                'gender' => $gender,
                
                // Budgets between 15k and 60k
                'budget_min' => rand(15, 30) * 1000,
                'budget_max' => rand(35, 60) * 1000,
                
                'preferred_city' => $location,
                'preferred_location' => $location, // Redundant but good for display
                
                'move_in_date' => Carbon::now()->addDays(rand(5, 60)),
                
                'is_smoker' => $faker->boolean(20), // 20% smokers
                'has_pets' => $faker->boolean(15), // 15% have pets
                
                'bio' => $faker->realText(100),
                
                // Compatibility Fields
                'pref_no_smoker' => $faker->boolean(60),
                'pref_pets_ok' => $faker->boolean(50),
                'pref_same_gender_only' => $faker->boolean(30),
                'pref_visitors_ok' => $faker->boolean(80),
                'pref_substance_free_required' => $faker->boolean(20),
                'uses_substances' => $faker->boolean(10),
                
                // Scales 1-5
                'cleanliness' => rand(2, 5),
                'noise_tolerance' => rand(1, 4),
                'sleep_schedule' => rand(1, 5),
                'study_focus' => rand(1, 5),
                'social_level' => rand(2, 5),
                
                'schedule_type' => $scheduleTypes[array_rand($scheduleTypes)],
                'occupation_field' => $faker->jobTitle,
            ]);
        }
        
        $this->command->info('Created 20 roommate profiles in ' . implode(', ', $targetLocations));
    }
}
