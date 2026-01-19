@extends('layouts.dwello')

@section('title', 'Find Flatmate - Dwello')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/matching.css') }}">
    <style>
        .btn-icon {
            width: 18px;
            height: 18px;
            margin-right: 8px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
    <div class="matching-page-wrapper">
        <div class="container mx-auto px-4 py-8 md:px-6">
            {{-- Page Title --}}
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-4xl font-bold text-gray-900 font-poppins mb-3">
                    Find Your Perfect Flatmate
                </h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
                    Discover compatible flatmates based on lifestyle preferences, schedules, and house rules
                </p>
            </div>

            {{-- Tabs --}}
            <div class="flex justify-center mb-12">
                <div
                    style="background: var(--gray-100); border-radius: 16px; padding: 6px; display: inline-flex; gap: 4px;">
                    <button class="tab-button active" onclick="switchTab('matches')" id="matchesTab">
                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Get Matches
                    </button>
                    <button class="tab-button" onclick="switchTab('compare')" id="compareTab">
                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Matching Properties
                    </button>
                </div>
            </div>

            {{-- Matches Tab Content --}}
            <div class="tab-content active" id="matchesContent">
                {{-- Filter Bar --}}
                <div class="bg-white rounded-2xl p-5 mb-8 shadow-sm">
                    <form method="GET" action="{{ route('roommates.index') }}"
                        style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                            <select name="city"
                                style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 14px; background: white;">
                                <option value="">Any City</option>
                                @foreach(config('cities') as $city)
                                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="budget_range"
                                style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 14px; background: white;">
                                <option value="">All Budgets</option>
                                <option value="low" {{ request('budget_range') == 'low' ? 'selected' : '' }}>₨10k - ₨25k
                                </option>
                                <option value="medium" {{ request('budget_range') == 'medium' ? 'selected' : '' }}>₨25k - ₨50k
                                </option>
                                <option value="high" {{ request('budget_range') == 'high' ? 'selected' : '' }}>₨50k+</option>
                            </select>

                            <select name="min_compatibility"
                                style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 14px; background: white;">
                                <option value="">Any Compatibility</option>
                                <option value="70">Compatibility: 70%+</option>
                                <option value="80">Compatibility: 80%+</option>
                                <option value="90">Compatibility: 90%+</option>
                            </select>

                            <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 14px;">
                                Apply
                            </button>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--gray-600); font-size: 14px white-space: nowrap;">Sort by:</span>
                            <select name="sort_by"
                                style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 14px; background: white;"
                                onchange="this.form.submit()">
                                <option value="best_match" {{ request('sort_by', 'best_match') == 'best_match' ? 'selected' : '' }}>Best Match</option>
                                <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest First
                                </option>
                                <option value="budget_low" {{ request('sort_by') == 'budget_low' ? 'selected' : '' }}>Budget:
                                    Low to High</option>
                                <option value="budget_high" {{ request('sort_by') == 'budget_high' ? 'selected' : '' }}>
                                    Budget: High to Low</option>
                            </select>
                        </div>
                    </form>
                </div>

                {{-- Profile Cards Grid --}}
                <div class="roommate-index-grid"
                    style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px;">
                    @forelse ($profiles as $profile)
                        @php
                            // Use calculated score if available (from Controller), otherwise fallback to heuristic or default
                            if (isset($profile->compatibility_score) && !is_null($profile->compatibility_score)) {
                                $compatibility = $profile->compatibility_score;
                            } else {
                                // Fallback for guests or if no score
                                $base = 70;
                                if ($profile->budget_min || $profile->budget_max)
                                    $base += 10;
                                if ($profile->preferred_city)
                                    $base += 5;
                                if ($profile->has_pets)
                                    $base -= 2;
                                if ($profile->is_smoker)
                                    $base -= 3;
                                $compatibility = max(50, min($base, 95));
                            }

                            // Calculate ring offset
                            $circumference = 339.3;
                            $offset = $circumference - ($compatibility / 100) * $circumference;

                            // Color logic
                            $strokeColor = '#EF4444'; // Red < 80
                            if ($compatibility >= 90)
                                $strokeColor = '#10B981'; // Green
                            elseif ($compatibility >= 80)
                                $strokeColor = '#F59E0B'; // Amber
                        @endphp

                        <div class="profile-card">
                            @php
                                $isFavorited = auth()->check() ? auth()->user()->favorites->contains($profile->id) : false;
                            @endphp
                            <div class="saved-indicator {{ $isFavorited ? 'saved' : '' }}" data-id="{{ $profile->id }}"
                                onclick="toggleSaved(this)">
                                <svg style="width: 16px; height: 16px; color: white;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>

                            <div class="text-center" style="margin-bottom: 20px;">
                                <div
                                    style="width: 80px; height: 80px; border-radius: 50%; background: var(--dwello-primary); color:white; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:30px; font-family: 'Poppins', sans-serif; overflow: hidden;">
                                    @if($profile->user && $profile->user->profile_photo_path)
                                        <img src="{{ Storage::url($profile->user->profile_photo_path) }}"
                                            alt="{{ $profile->display_name }}"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        {{ strtoupper(substr($profile->display_name ?? 'U', 0, 1)) }}
                                    @endif
                                </div>

                                <h3
                                    style="font-size: 20px; font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gray-900); margin-bottom: 4px;">
                                    {{ $profile->display_name ?? 'Unnamed User' }}
                                </h3>
                                <p style="color: var(--gray-600); font-size: 14px;">
                                    {{ $profile->preferred_city ?? 'Open to any location' }}
                                </p>
                            </div>

                            {{-- Compatibility Ring --}}
                            <div class="compatibility-ring" style="margin-bottom: 20px;">
                                <svg width="120" height="120">
                                    <circle cx="60" cy="60" r="54" stroke="#E5E7EB" stroke-width="8" fill="none" />
                                    <circle cx="60" cy="60" r="54" stroke="{{ $strokeColor }}" stroke-width="8" fill="none"
                                        stroke-dasharray="339.3" stroke-dashoffset="{{ $offset }}" stroke-linecap="round" />
                                </svg>
                                <div class="percentage">{{ $compatibility }}%</div>
                            </div>

                            {{-- Compatibility Chips --}}
                            <div style="margin-bottom: 20px;">
                                <div style="display: flex; flex-wrap: wrap; gap: 6px; justify-content: center;">
                                    @if($profile->is_smoker)
                                        <span class="compatibility-chip chip-conflict">
                                            <span>●</span> Smoker
                                        </span>
                                    @else
                                        <span class="compatibility-chip chip-match">
                                            <span>●</span> Non-smoker
                                        </span>
                                    @endif

                                    <span class="compatibility-chip chip-neutral"
                                        style="background: var(--gray-100); color: var(--gray-700);">
                                        <span>●</span>
                                        {{ App\Models\RoommateProfile::getLabel('sleep_schedule', $profile->sleep_schedule) }}
                                    </span>
                                    <span class="compatibility-chip chip-neutral"
                                        style="background: var(--gray-100); color: var(--gray-700);">
                                        <span>●</span>
                                        {{ App\Models\RoommateProfile::getLabel('noise_tolerance', $profile->noise_tolerance) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Quick Stats --}}
                            <div
                                style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; padding: 16px; background: var(--gray-50); border-radius: 12px;">
                                <div class="text-center">
                                    <div style="font-size: 20px; font-weight: bold; color: var(--dwello-primary);">
                                        @if($profile->budget_max)
                                            @if($profile->budget_min && $profile->budget_min < $profile->budget_max)
                                                ₨{{ number_format($profile->budget_min / 1000, 0) }}K -
                                                {{ number_format($profile->budget_max / 1000, 0) }}K
                                            @else
                                                ₨{{ number_format($profile->budget_max / 1000, 0) }}K
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div style="font-size: 12px; color: var(--gray-600);">Budget</div>
                                </div>
                                <div class="text-center">
                                    <div style="font-size: 20px; font-weight: bold; color: var(--dwello-primary);">
                                        {{ Str::limit($profile->preferred_location ?? 'Any', 10) }}
                                    </div>
                                    <div style="font-size: 12px; color: var(--gray-600);">Preferred</div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                <a href="{{ route('roommates.show', $profile) }}" class="btn btn-outline"
                                    style="padding: 8px 16px; font-size: 14px;">
                                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    View Profile
                                </a>
                                <form action="{{ route('conversations.startRoommate', $profile->user_id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary"
                                        style="padding: 8px 16px; font-size: 14px; width: 100%;">
                                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-1 md:col-span-3 flex flex-col items-center justify-center py-12 text-center bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No roommates found</h3>
                            <p class="text-gray-500 max-w-sm mb-6">
                                We couldn't find any profiles matching your search. Try broadening your criteria.
                            </p>
                            <a href="{{ route('roommates.index') }}" class="btn btn-outline">
                                Clear Filters
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Load More --}}
                @if($profiles->hasPages())
                    <div class="text-center" style="margin-top: 32px;">
                        {{ $profiles->links() }}
                    </div>
                @endif
            </div>

            {{-- Compare Profiles Tab Content --}}
            <div class="tab-content" id="compareContent">
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <div class="text-center mb-10">
                        <h3 class="text-2xl font-bold text-gray-900 font-poppins mb-2">
                            Homes That Match Your Vibe
                        </h3>
                        <p class="text-gray-600">
                            Based on your profile preferences, here are some properties you might like.
                        </p>
                    </div>

                    @if(isset($matchingPropertiesParams) && $matchingPropertiesParams->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                            @foreach($matchingPropertiesParams as $property)
                                <div
                                    class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg transition group">
                                    <div class="relative h-48 bg-gray-200">
                                        @php
                                            $photo = $property->photos->first();
                                            $src = $photo ? Storage::url($photo->path) : null;
                                        @endphp
                                        @if($src)
                                            <img src="{{ $src }}" alt="{{ $property->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div
                                            class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-bold text-gray-900 shadow-sm">
                                            ₨{{ number_format($property->monthly_rent / 1000, 1) }}k<span
                                                class="text-xs font-normal text-gray-500">/mo</span>
                                        </div>
                                        <div
                                            class="absolute bottom-3 left-3 bg-black/60 backdrop-blur px-3 py-1 rounded-full text-xs font-medium text-white">
                                            {{ ucfirst($property->property_type) }}
                                        </div>
                                    </div>

                                    <div class="p-5">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <h4 class="font-bold text-gray-900 truncate" title="{{ $property->title }}">
                                                    {{ Str::limit($property->title, 25) }}
                                                </h4>
                                                <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    {{ $property->city }}
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-50 text-sm text-gray-600">
                                            <div class="flex items-center gap-1">
                                                <span>🛏️</span> {{ $property->bedrooms }} Beds
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span>🚿</span> {{ $property->bathrooms }} Baths
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span>📏</span> {{ $property->size_sqft }} sqft
                                            </div>
                                        </div>

                                        <a href="{{ route('properties.show', $property) }}"
                                            class="mt-4 w-full block text-center bg-gray-50 hover:bg-gray-100 text-gray-900 font-medium py-2.5 rounded-xl transition border border-gray-200">
                                            View Property
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-2xl mb-12">
                            <div
                                class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">No specific matches found yet</h4>
                            <p class="text-gray-500 max-w-sm mx-auto mb-6">
                                We couldn't find properties exactly matching your profile preferences (City, Budget, etc.).
                            </p>
                            @auth
                                <a href="{{ route('profile.edit') }}" class="text-dwello-primary font-medium hover:underline">Update
                                    your preferences</a>
                            @else
                                <a href="{{ route('login') }}" class="text-dwello-primary font-medium hover:underline">Log in to get
                                    matches</a>
                            @endauth
                        </div>
                    @endif

                    <div class="text-center">
                        <a href="{{ route('properties.index') }}"
                            class="btn btn-primary px-8 py-3 text-lg shadow-lg shadow-orange-500/20 hover:scale-105 transition transform">
                            View All Properties
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/matching.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.add('matching-page');
        });

        // Pass server data to JS
        window.serverProfiles = @json($profiles->items());
        window.userProfile = @json($userProfile);
        window.userProfile = @json(auth()->user() ? auth()->user()->roommateProfile : null);

        // Debug
        console.log('Available profiles:', window.serverProfiles);
        console.log('My profile:', window.userProfile);
    </script>
@endpush