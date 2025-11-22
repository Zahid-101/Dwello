{{-- resources/views/roommates/index.blade.php --}}
@extends('layouts.dwello')

@section('title', 'Find Flatmate - Dwello')

@section('content')
    <div class="container" style="padding: 32px 24px;">
        {{-- Page title --}}
        <div class="text-center" style="margin-bottom: 32px;">
            <h2 style="font-size: 36px; font-family: 'Poppins', sans-serif; font-weight: bold; color: var(--gray-900); margin-bottom: 12px;">
                Find Your Perfect Flatmate
            </h2>
            <p style="font-size: 18px; color: var(--gray-600); max-width: 600px; margin: 0 auto;">
                Discover compatible flatmates based on lifestyle preferences, schedules, and house rules
            </p>
        </div>

        {{-- Tabs --}}
        <div class="flex justify-center" style="margin-bottom: 32px;">
            <div style="background: var(--gray-100); border-radius: 16px; padding: 6px;">
                <button class="tab-button active" onclick="switchTab('matches')" id="matchesTab">
                    Get Matches
                </button>
                <button class="tab-button" onclick="switchTab('compare')" id="compareTab">
                    Compare Profiles
                </button>
            </div>
        </div>

        {{-- Matches tab --}}
        <div class="tab-content active" id="matchesContent">
            {{-- Simple filters bar (for now only visual) --}}
            <div style="background: white; border-radius: 16px; padding: 20px; margin-bottom: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <div class="flex items-center justify-between">
                    <div class="flex items-center" style="gap: 16px;">
                        <select class="input" style="border-radius: 12px;">
                            <option>All Locations</option>
                        </select>
                        <select class="input" style="border-radius: 12px;">
                            <option>All Budgets</option>
                        </select>
                        <select class="input" style="border-radius: 12px;">
                            <option>Compatibility: 70%+</option>
                        </select>
                    </div>
                    <div class="flex items-center" style="gap: 12px;">
                        <span style="color: var(--gray-600); font-size: 14px;">Sort by:</span>
                        <select class="input" style="border-radius: 12px;">
                            <option>Best Match</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Profiles grid --}}
            <div class="grid grid-3 gap-6" style="margin-bottom: 32px;">
                @forelse ($profiles as $profile)
                    <div class="profile-card">
                        <div class="saved-indicator" onclick="toggleSaved(this)">
                            <svg style="width: 16px; height: 16px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>

                        <div class="text-center" style="margin-bottom: 20px;">
                            {{-- For now no avatar upload, just initials --}}
                            <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--dwello-primary); color:white; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:30px;">
                                {{ strtoupper(substr($profile->display_name, 0, 1)) }}
                            </div>
                            <h3 style="font-size: 20px; font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gray-900); margin-bottom: 4px;">
                                {{ $profile->display_name }}
                            </h3>
                            <p style="color: var(--gray-600); font-size: 14px;">
                                {{ $profile->preferred_city ?? 'Preferred city not set' }}
                            </p>
                        </div>

                        {{-- Compatibility ring – for now simple 80–95% based on budget presence --}}
                        @php
                            $base = 80;
                            if ($profile->budget_min || $profile->budget_max) $base += 5;
                            if ($profile->is_smoker == (optional(auth()->user()->roommateProfile)->is_smoker)) $base += 5;
                            $compatibility = min($base, 98);
                            $circumference = 339.3;
                            $offset = $circumference - ($compatibility / 100) * $circumference;
                        @endphp

                        <div class="compatibility-ring" style="margin-bottom: 20px;">
                            <svg width="120" height="120">
                                <circle cx="60" cy="60" r="54" stroke="#E5E7EB" stroke-width="8" fill="none"/>
                                <circle cx="60" cy="60" r="54"
                                        stroke="#10B981"
                                        stroke-width="8"
                                        fill="none"
                                        stroke-dasharray="339.3"
                                        stroke-dashoffset="{{ $offset }}"
                                        stroke-linecap="round"/>
                            </svg>
                            <div class="percentage">{{ $compatibility }}%</div>
                        </div>

                        {{-- Quick stats --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; padding: 16px; background: var(--gray-50); border-radius: 12px;">
                            <div class="text-center">
                                <div style="font-size: 20px; font-weight: bold; color: var(--dwello-primary);">
                                    @if($profile->budget_max)
                                        ₨{{ number_format($profile->budget_max, 0) }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div style="font-size: 12px; color: var(--gray-600);">Budget</div>
                            </div>
                            <div class="text-center">
                                <div style="font-size: 20px; font-weight: bold; color: var(--dwello-primary);">
                                    {{ $profile->preferred_city ?? 'Any' }}
                                </div>
                                <div style="font-size: 12px; color: var(--gray-600);">Preferred</div>
                            </div>
                        </div>

                        {{-- Action buttons --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <button class="btn btn-outline" style="padding: 8px 16px; font-size: 14px;">
                                View Profile
                            </button>
                            <button class="btn btn-primary" style="padding: 8px 16px; font-size: 14px;">
                                Message
                            </button>
                        </div>
                    </div>
                @empty
                    <p style="color: var(--gray-600);">No roommate profiles yet.</p>
                @endforelse
            </div>

            <div class="text-center">
                {{ $profiles->links() }}
            </div>
        </div>

        {{-- Compare tab – keep mostly static for now --}}
        <div class="tab-content" id="compareContent">
            <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
                <h3 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: bold; color: var(--gray-900); margin-bottom: 24px; text-align: center;">
                    Compare Two Profiles
                </h3>
                <p style="text-align:center; color: var(--gray-600);">
                    (You can wire this to real data later – for now it’s a nice UI shell.)
                </p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        document.getElementById(tabName + 'Tab').classList.add('active');
        document.getElementById(tabName + 'Content').classList.add('active');
    }

    function toggleSaved(el) {
        el.classList.toggle('saved');
    }
</script>
@endpush