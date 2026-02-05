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
                ->with('user')
                ->inRandomOrder() // Shuffle them so different ones show up first
                ->get();
            $view->with('boostedAds', $boostedAds);
        });
    }
}
