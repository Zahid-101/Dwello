<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer(['layouts.navigation', 'layouts.dwello'], function ($view) {
            $unreadMessagesCount = 0;
            if (auth()->check()) {
                $unreadMessagesCount = \App\Models\Message::whereHas('conversation', function ($query) {
                    $query->where('user_one_id', auth()->id())
                        ->orWhere('user_two_id', auth()->id());
                })
                    ->where('sender_id', '!=', auth()->id())
                    ->whereNull('read_at')
                    ->count();
            }
            $view->with('unreadMessagesCount', $unreadMessagesCount);
        });

        \Illuminate\Support\Facades\View::composer('home', function ($view) {
            $boostedAds = \App\Models\BoostedAd::where('is_active', true)
                ->with(['user', 'property'])
                ->get();

            // Personalize if logged in with profile
            if (auth()->check() && auth()->user()->roommateProfile) {
                $profile = auth()->user()->roommateProfile;

                // Filter first (Strict Budget)
                $boostedAds = $boostedAds->filter(function ($ad) use ($profile) {
                    if (!$ad->property)
                        return false;

                    // Strict Budget Filter: Show only if rent is within max budget
                    if ($profile->budget_max && $ad->property->monthly_rent > $profile->budget_max) {
                        return false;
                    }

                    return true;
                });

                // Then Sort by City Match
                $boostedAds = $boostedAds->sortByDesc(function ($ad) use ($profile) {
                    if ($ad->property && $profile->preferred_city && stripos($ad->property->city, $profile->preferred_city) !== false) {
                        return 1;
                    }
                    return 0;
                });
            } else {
                $boostedAds = $boostedAds->shuffle();
            }

            $view->with('boostedAds', $boostedAds);
        });
    }
}
