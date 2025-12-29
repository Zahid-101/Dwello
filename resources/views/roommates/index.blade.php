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
            <div style="background: var(--gray-100); border-radius: 16px; padding: 6px; display: inline-flex; gap: 4px;">
                <button class="tab-button active" onclick="switchTab('matches')" id="matchesTab">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Get Matches
                </button>
                <button class="tab-button" onclick="switchTab('compare')" id="compareTab">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Compare Profiles
                </button>
            </div>
        </div>

        {{-- Matches Tab Content --}}
        <div class="tab-content active" id="matchesContent">
            {{-- Filter Bar --}}
            <div class="bg-white rounded-2xl p-5 mb-8 shadow-sm">
                <form method="GET" action="{{ route('roommates.index') }}" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        <select name="city" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 14px; background: white;">
                            <option value="">Any City</option>
                            @foreach(config('cities') as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                        
                        <select name="budget_range" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 14px; background: white;">
                            <option value="">All Budgets</option>
                            <option value="low" {{ request('budget_range') == 'low' ? 'selected' : '' }}>₨10k - ₨25k</option>
                            <option value="medium" {{ request('budget_range') == 'medium' ? 'selected' : '' }}>₨25k - ₨50k</option>
                            <option value="high" {{ request('budget_range') == 'high' ? 'selected' : '' }}>₨50k+</option>
                        </select>

                        <select name="min_compatibility" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 14px; background: white;">
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
                        <select name="sort_by" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 14px; background: white;" onchange="this.form.submit()">
                            <option value="best_match" {{ request('sort_by', 'best_match') == 'best_match' ? 'selected' : '' }}>Best Match</option>
                            <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="budget_low" {{ request('sort_by') == 'budget_low' ? 'selected' : '' }}>Budget: Low to High</option>
                            <option value="budget_high" {{ request('sort_by') == 'budget_high' ? 'selected' : '' }}>Budget: High to Low</option>
                        </select>
                    </div>
                </form>
            </div>

            {{-- Profile Cards Grid --}}
            <div class="roommate-index-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px;">
                @forelse ($profiles as $profile)
                    @php
                        // Use calculated score if available (from Controller), otherwise fallback to heuristic or default
                        if (isset($profile->compatibility_score) && !is_null($profile->compatibility_score)) {
                            $compatibility = $profile->compatibility_score;
                        } else {
                            // Fallback for guests or if no score
                            $base = 70;
                            if ($profile->budget_min || $profile->budget_max) $base += 10;
                            if ($profile->preferred_city) $base += 5;
                            if ($profile->has_pets) $base -= 2;
                            if ($profile->is_smoker) $base -= 3;
                            $compatibility = max(50, min($base, 95));
                        }
                        
                        // Calculate ring offset
                        $circumference = 339.3;
                        $offset = $circumference - ($compatibility / 100) * $circumference;
                        
                        // Color logic
                        $strokeColor = '#EF4444'; // Red < 80
                        if ($compatibility >= 90) $strokeColor = '#10B981'; // Green
                        elseif ($compatibility >= 80) $strokeColor = '#F59E0B'; // Amber
                    @endphp

                    <div class="profile-card">
                        @php
                            $isFavorited = auth()->check() ? auth()->user()->favorites->contains($profile->id) : false;
                        @endphp
                        <div class="saved-indicator {{ $isFavorited ? 'saved' : '' }}" 
                             data-id="{{ $profile->id }}" 
                             onclick="toggleSaved(this)">
                            <svg style="width: 16px; height: 16px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>

                        <div class="text-center" style="margin-bottom: 20px;">
                             <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--dwello-primary); color:white; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:30px; font-family: 'Poppins', sans-serif;">
                                {{ strtoupper(substr($profile->display_name ?? 'U', 0, 1)) }}
                            </div>
                            
                            <h3 style="font-size: 20px; font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gray-900); margin-bottom: 4px;">
                                {{ $profile->display_name ?? 'Unnamed User' }}
                            </h3>
                            <p style="color: var(--gray-600); font-size: 14px;">
                                {{ $profile->preferred_city ?? 'Open to any location' }}
                            </p>
                        </div>

                        {{-- Compatibility Ring --}}
                        <div class="compatibility-ring" style="margin-bottom: 20px;">
                            <svg width="120" height="120">
                                {{-- Background Circle --}}
                                <circle cx="60" cy="60" r="54" stroke="#f3f4f6" stroke-width="8" fill="none" 
                                    @guest stroke-dasharray="8 4" @endguest 
                                />
                                
                                @auth
                                    {{-- Filled Circle (Only for auth) --}}
                                    <circle cx="60" cy="60" r="54" 
                                            stroke="{{ $strokeColor }}" 
                                            stroke-width="8" fill="none" 
                                            stroke-dasharray="339.3" 
                                            stroke-dashoffset="{{ $offset }}" 
                                            stroke-linecap="round"
                                            style="transition: stroke-dashoffset 1s ease-out;"
                                    />
                                @endauth
                            </svg>
                            
                            <div class="percentage" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;">
                                @auth
                                    <span style="font-size: 24px; font-weight: 700; color: var(--gray-900);">{{ $compatibility }}<span style="font-size: 14px;">%</span></span>
                                @else
                                    <a href="{{ route('login', ['role' => 'tenant']) }}" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; color: var(--gray-400); transition: all 0.2s;" onmouseover="this.style.color='var(--dwello-primary)'" onmouseout="this.style.color='var(--gray-400)'">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 24px; height: 24px; margin-bottom: 2px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                        <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Unlock</span>
                                    </a>
                                @endauth
                            </div>
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
                                
                                <span class="compatibility-chip chip-neutral" style="background: var(--gray-100); color: var(--gray-700);">
                                    <span>●</span> {{ App\Models\RoommateProfile::getLabel('sleep_schedule', $profile->sleep_schedule) }}
                                </span>
                                <span class="compatibility-chip chip-neutral" style="background: var(--gray-100); color: var(--gray-700);">
                                    <span>●</span> {{ App\Models\RoommateProfile::getLabel('noise_tolerance', $profile->noise_tolerance) }}
                                </span>
                            </div>
                        </div>

                        {{-- Quick Stats --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; padding: 16px; background: var(--gray-50); border-radius: 12px;">
                            <div class="text-center">
                                <div style="font-size: 20px; font-weight: bold; color: var(--dwello-primary);">
                                    @if($profile->budget_max)
                                        @if($profile->budget_min && $profile->budget_min < $profile->budget_max)
                                            ₨{{ number_format($profile->budget_min/1000, 0) }}K - {{ number_format($profile->budget_max/1000, 0) }}K
                                        @else
                                            ₨{{ number_format($profile->budget_max/1000, 0) }}K
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
                            <a href="{{ route('roommates.show', $profile) }}" class="btn btn-outline" style="padding: 8px 16px; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <svg class="btn-icon" style="width: 18px; height: 18px; margin: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>View Profile</span>
                            </a>
                            <form action="{{ route('conversations.startRoommate', $profile->user_id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 14px; width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                    <svg class="btn-icon" style="width: 18px; height: 18px; margin: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <span>Message</span>
                                </button>
                            </form>
                        </div>
                        
                        <div style="margin-top: 12px; text-align: center;">
                            <button class="text-xs text-gray-400 hover:text-red-500 transition" 
                                    data-id="{{ $profile->user_id }}" 
                                    onclick="rejectUser(this)">
                                Not interested
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-3 flex flex-col items-center justify-center py-12 text-center bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
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
            <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); min-height: 400px;">
                <h3 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: bold; color: var(--gray-900); margin-bottom: 24px; text-align: center;">
                    Compare Two Profiles
                </h3>
                
                @auth
                    {{-- Profile Selectors --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; color: var(--gray-700); margin-bottom: 8px;">Profile A (You)</label>
                            <select style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 12px; font-size: 14px;">
                                <option>{{ Auth::user()->name }}</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; color: var(--gray-700); margin-bottom: 8px;">Profile B (Compare with)</label>
                             <select id="profileBSelect" style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 12px; font-size: 14px;" onchange="showComparison()">
                                <option value="">Select a profile to compare</option>
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}">{{ $profile->display_name }} ({{ $profile->preferred_city }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Comparison Results (Initially Hidden) --}}
                    <div id="comparisonResults" style="display: none;">
                        {{-- Overall Compatibility --}}
                        <div class="text-center" style="margin-bottom: 32px;">
                            <div class="compatibility-ring" style="margin-bottom: 16px;">
                                <svg width="120" height="120">
                                    <circle cx="60" cy="60" r="54" stroke="#E5E7EB" stroke-width="8" fill="none"/>
                                    <circle id="compatibilityCircle" cx="60" cy="60" r="54" stroke="#10B981" stroke-width="8" fill="none" 
                                            stroke-dasharray="339.3" stroke-dashoffset="67.9" stroke-linecap="round"/>
                                </svg>
                                <div class="percentage" id="compatibilityScore">92%</div>
                            </div>
                            <h4 style="font-size: 18px; font-weight: 600; color: var(--gray-900); margin-bottom: 8px;">Overall Compatibility</h4>
                            <p style="color: var(--gray-600); font-size: 14px;">Based on your preferences and weights</p>
                        </div>

                        {{-- Category Breakdown --}}
                        <div style="margin-bottom: 32px;">
                            <h4 style="font-size: 18px; font-weight: 600; color: var(--gray-900); margin-bottom: 16px;">Category Breakdown</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Category Bars --}}
                                <div id="categoryBarsContainer">
                                    {{-- Filled via JS --}}
                                    {{-- Budget --}}
                                    <div style="margin-bottom: 16px;">
                                        <div class="flex justify-between items-center" style="margin-bottom: 4px;">
                                            <span style="font-weight: 500; color: var(--gray-900);">Budget Match</span>
                                            <span style="font-size: 14px; color: var(--gray-600);" id="budgetScore">--</span>
                                        </div>
                                        <div style="height: 8px; background: var(--gray-200); border-radius: 4px; overflow: hidden;">
                                            <div id="budgetBar" style="height: 100%; width: 0%; background: var(--green-500); transition: width 0.5s ease;"></div>
                                        </div>
                                    </div>
                                    {{-- Location --}}
                                    <div style="margin-bottom: 16px;">
                                        <div class="flex justify-between items-center" style="margin-bottom: 4px;">
                                            <span style="font-weight: 500; color: var(--gray-900);">Location Match</span>
                                            <span style="font-size: 14px; color: var(--gray-600);" id="locationScore">--</span>
                                        </div>
                                        <div style="height: 8px; background: var(--gray-200); border-radius: 4px; overflow: hidden;">
                                            <div id="locationBar" style="height: 100%; width: 0%; background: var(--green-500); transition: width 0.5s ease;"></div>
                                        </div>
                                    </div>
                                    {{-- Lifestyle --}}
                                    <div style="margin-bottom: 16px;">
                                        <div class="flex justify-between items-center" style="margin-bottom: 4px;">
                                            <span style="font-weight: 500; color: var(--gray-900);">Lifestyle Habits</span>
                                            <span style="font-size: 14px; color: var(--gray-600);" id="lifestyleScore">--</span>
                                        </div>
                                        <div style="height: 8px; background: var(--gray-200); border-radius: 4px; overflow: hidden;">
                                            <div id="lifestyleBar" style="height: 100%; width: 0%; background: var(--green-500); transition: width 0.5s ease;"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Detailed Breakdown --}}
                                <div>
                                    <h5 style="font-weight: 600; color: var(--gray-900); margin-bottom: 12px;">Match Details</h5>
                                    <div style="background: var(--gray-50); border-radius: 12px; padding: 16px;" id="matchDetails">
                                        {{-- Populated by JS --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-center" style="gap: 16px;">
                            <button id="compareSaveBtn" class="btn btn-outline" onclick="handleCompareSave()" disabled>
                                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span id="compareSaveText">Save Comparison</span>
                            </button>
                            
                            <form id="compareMessageForm" method="POST" action="#">
                                @csrf
                                <button id="compareMessageBtn" type="submit" class="btn btn-primary" disabled>
                                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Start Conversation
                                </button>
                            </form>

                            <button id="compareShareBtn" class="btn btn-outline" onclick="handleCompareShare()" disabled>
                                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                Share Comparison
                            </button>
                        </div>
                    </div>

                    {{-- Empty State --}}
                    <div id="emptyState" class="text-center" style="padding: 48px 0;">
                        <svg style="width: 64px; height: 64px; color: var(--gray-400); margin: 0 auto 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <h4 style="font-size: 18px; font-weight: 600; color: var(--gray-900); margin-bottom: 8px;">Select Two Profiles to Compare</h4>
                        <p style="color: var(--gray-600);">Choose a profile from the dropdown to see detailed compatibility analysis</p>
                    </div>
                @else
                    {{-- Guest Lock State --}}
                    <div class="text-center flex flex-col items-center justify-center p-8">
                        <div style="width: 80px; height: 80px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--gray-400)" style="width: 40px; height: 40px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <h3 style="font-size: 20px; font-weight: 600; color: var(--gray-900); margin-bottom: 12px;">Login to Compare Profiles</h3>
                        <p style="color: var(--gray-600); max-width: 400px; margin-bottom: 32px;">
                            Unlock detailed compatibility breakdowns and side-by-side comparisons to find your perfect match.
                        </p>
                        <a href="{{ route('login', ['role' => 'tenant']) }}" class="btn btn-primary" style="padding: 12px 32px;">
                            Login to Unlock
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
    
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/matching.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
