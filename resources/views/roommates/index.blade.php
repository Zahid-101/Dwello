@extends('layouts.dwello')

@section('title', 'Find Flatmate - Dwello')

@section('content')
<div class="container" style="padding: 32px 24px;">
    {{-- Page title --}}
    <div class="text-center" style="margin-bottom: 32px;">
        <h2 style="font-size: 32px; font-family: 'Poppins', sans-serif; font-weight: 700; color: var(--gray-900); margin-bottom: 8px;">
            Find Your Perfect Flatmate
        </h2>
        <p style="font-size: 16px; color: var(--gray-600); max-width: 600px; margin: 0 auto;">
            Discover compatible flatmates based on budget, location and lifestyle preferences.
        </p>
    </div>



    {{-- Matches tab --}}
    <div class="tab-content active" id="matchesContent">
        {{-- Filters bar --}}
<div style="background: white; border-radius: 16px; padding: 20px; margin-bottom: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <form method="GET" action="{{ route('roommates.index') }}" class="flex items-center justify-between" style="gap: 16px; flex-wrap: wrap;">
        <div class="flex items-center" style="gap: 12px; flex-wrap: wrap;">
            <input
                type="text"
                name="city"
                placeholder="Preferred city"
                value="{{ request('city') }}"
                class="input"
                style="border-radius: 12px; min-width: 160px;"
            >

            <input
                type="number"
                step="100"
                name="min_budget"
                placeholder="Min budget"
                value="{{ request('min_budget') }}"
                class="input"
                style="border-radius: 12px; width: 130px;"
            >

            <input
                type="number"
                step="100"
                name="max_budget"
                placeholder="Max budget"
                value="{{ request('max_budget') }}"
                class="input"
                style="border-radius: 12px; width: 130px;"
            >

            <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:var(--gray-600);">
                <input type="checkbox" name="has_pets" {{ request()->boolean('has_pets') ? 'checked' : '' }}>
                <span>Has pets</span>
            </label>

            <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:var(--gray-600);">
                <input type="checkbox" name="is_smoker" {{ request()->boolean('is_smoker') ? 'checked' : '' }}>
                <span>Smoker</span>
            </label>
        </div>

        <div class="flex items-center" style="gap: 10px;">
            <button type="submit" class="btn btn-primary" style="border-radius: 12px; padding: 8px 16px;">
                Apply
            </button>
            <a href="{{ route('roommates.index') }}" class="btn btn-outline" style="border-radius: 12px; padding: 8px 16px;">
                Clear
            </a>
        </div>
    </form>
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

                    {{-- Avatar + name --}}
                    <div class="text-center" style="margin-bottom: 20px;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--dwello-primary); color:white; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:30px;">
                            {{ strtoupper(substr($profile->display_name ?? 'U', 0, 1)) }}
                        </div>
                        <h3 style="font-size: 20px; font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gray-900); margin-bottom: 4px;">
                            {{ $profile->display_name ?? 'Unnamed user' }}
                        </h3>
                        <p style="color: var(--gray-600); font-size: 14px;">
                            {{ $profile->preferred_city ?? 'Preferred city not set' }}
                        </p>
                    </div>

                    {{-- Compatibility ring (fake but stable, only uses this profile) --
                    @php
                        $base = 70;
                        if ($profile->budget_min || $profile->budget_max) $base += 10;
                        if ($profile->preferred_city) $base += 5;
                        if ($profile->has_pets) $base -= 2;
                        if ($profile->is_smoker) $base -= 3;
                        $compatibility = max(50, min($base, 95));
                        $circumference = 339.3;
                        $offset = $circumference - ($compatibility / 100) * $circumference;
                    @endphp
                    }}
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
                            <div style="font-size: 18px; font-weight: bold; color: var(--dwello-primary);">
                                @if($profile->budget_max)
                                    ₨{{ number_format($profile->budget_max, 0) }}
                                @else
                                    N/A
                                @endif
                            </div>
                            <div style="font-size: 12px; color: var(--gray-600);">Budget max</div>
                        </div>
                        <div class="text-center">
                            <div style="font-size: 18px; font-weight: bold; color: var(--dwello-primary);">
                                {{ $profile->preferred_location ?? 'Flexible' }}
                            </div>
                            <div style="font-size: 12px; color: var(--gray-600);">Preferred area</div>
                        </div>
                    </div>

                    {{-- Bio + actions --}}
                    <p style="font-size: 13px; color: var(--gray-600); margin-bottom: 16px; min-height: 48px;">
                        {{ $profile->bio ? \Illuminate\Support\Str::limit($profile->bio, 120) : 'No bio added yet.' }}
                    </p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        {{-- Not built yet → under development --}}
                        <a href="{{ route('roommates.show', $profile) }}" class="btn btn-outline" style="padding: 8px 16px; font-size: 14px;">
                            View Profile
                        </a>
                        {{--Future feature 
                        <a href="{{ route('under-development') }}" class="btn btn-primary" style="padding: 8px 16px; font-size: 14px;">
                            Message
                        </a>
                        --}}
                    </div>
                </div>
            @empty
                <p style="color: var(--gray-600);">No roommate profiles yet. Be the first to create one!</p>
            @endforelse
        </div>

        <div class="text-center">
            {{ $profiles->links() }}
        </div>
    </div>

    {{-- Compare tab shell --}}
    <div class="tab-content" id="compareContent">
        <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
            <h3 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: 700; color: var(--gray-900); margin-bottom: 16px; text-align: center;">
                Compare two profiles
            </h3>
            <p style="text-align:center; color: var(--gray-600); font-size:14px;">
                This feature is still under development. In a later sprint you’ll be able to
                pick two profiles and see a detailed side-by-side comparison.
            </p>
            <div style="text-align:center; margin-top:20px;">
                <a href="{{ route('under-development') }}" class="btn btn-outline" style="border-radius:16px;">
                    Learn more
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('form-scripts')
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
