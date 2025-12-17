@extends('layouts.dwello')

@section('title', $roommateProfile->display_name . ' - Roommate Profile')

@section('content')
<div class="container" style="padding: 32px 24px;">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('roommates.index') }}" class="btn btn-outline" style="border-radius: 12px; padding: 8px 16px;">
            &larr; Back to Listings
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Left Column: Profile Card --}}
        <div class="md:col-span-2">
            <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 24px;">
                <div class="flex items-center" style="gap: 24px; margin-bottom: 24px;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--dwello-primary); color:white; display:flex; align-items:center; justify-content:center; font-size:40px;">
                        {{ strtoupper(substr($roommateProfile->display_name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <h1 style="font-size: 28px; font-family: 'Poppins', sans-serif; font-weight: 700; color: var(--gray-900);">
                            {{ $roommateProfile->display_name }}
                        </h1>
                        <p style="color: var(--gray-600); font-size: 16px;">
                            {{ $roommateProfile->age ? $roommateProfile->age . ' years old • ' : '' }}
                            {{ ucfirst($roommateProfile->gender ?? 'Not specified') }}
                        </p>
                        <p style="color: var(--gray-600); font-size: 14px; margin-top: 4px;">
                            Looking in: <strong>{{ $roommateProfile->preferred_city ?? 'Anywhere' }}</strong>
                        </p>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px; color: var(--gray-900);">About Me</h3>
                    <p style="color: var(--gray-600); line-height: 1.6;">
                        {{ $roommateProfile->bio ?? 'No bio provided.' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-6" style="margin-bottom: 24px;">
                    <div style="background: var(--gray-50); padding: 16px; border-radius: 12px;">
                        <div style="font-size: 12px; color: var(--gray-600); margin-bottom: 4px;">Budget Range</div>
                        <div style="font-size: 18px; font-weight: 600; color: var(--dwello-primary);">
                            @if($roommateProfile->budget_min || $roommateProfile->budget_max)
                                ₨{{ number_format($roommateProfile->budget_min) }} - ₨{{ number_format($roommateProfile->budget_max) }}
                            @else
                                Flexible
                            @endif
                        </div>
                    </div>
                    <div style="background: var(--gray-50); padding: 16px; border-radius: 12px;">
                        <div style="font-size: 12px; color: var(--gray-600); margin-bottom: 4px;">Move-in Date</div>
                        <div style="font-size: 18px; font-weight: 600; color: var(--dwello-primary);">
                            {{ $roommateProfile->move_in_date ? $roommateProfile->move_in_date->format('M d, Y') : 'Unknown' }}
                        </div>
                    </div>
                </div>
                
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--gray-900);">Lifestyle & Preferences</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-2">
                        <span style="font-size: 18px;">🚬</span>
                        <span style="color: var(--gray-700);">{{ $roommateProfile->is_smoker ? 'Smoker' : 'Non-smoker' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span style="font-size: 18px;">🐾</span>
                        <span style="color: var(--gray-700);">{{ $roommateProfile->has_pets ? 'Has Pets' : 'No Pets' }}</span>
                    </div>
                    @if($roommateProfile->occupation_field)
                    <div class="flex items-center gap-2">
                        <span style="font-size: 18px;">💼</span>
                        <span style="color: var(--gray-700);">{{ $roommateProfile->occupation_field }}</span>
                    </div>
                    @endif
                    @if($roommateProfile->schedule_type)
                    <div class="flex items-center gap-2">
                        <span style="font-size: 18px;">⏰</span>
                        <span style="color: var(--gray-700);">{{ ucfirst($roommateProfile->schedule_type) }} Schedule</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Compatibility --}}
        <div>
            @if(isset($compatibility))
                <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); position: sticky; top: 24px;">
                    <h3 style="font-size: 20px; font-weight: 700; color: var(--gray-900); margin-bottom: 16px;">
                        Compatibility
                    </h3>

                    @if(is_null($compatibility['score']))
                        @if(isset($compatibility['message']))
                            <div style="padding: 12px; background: var(--gray-100); border-radius: 8px; color: var(--gray-600); font-size: 14px;">
                                {{ $compatibility['message'] }}
                            </div>
                        @else
                            <div style="padding: 12px; background: var(--gray-100); border-radius: 8px; color: var(--gray-600); font-size: 14px;">
                                Complete your preferences to see compatibility!
                            </div>
                        @endif
                    @elseif($compatibility['score'] === 0)
                        <div style="padding: 16px; background: #FEE2E2; border: 1px solid #F87171; border-radius: 12px; margin-bottom: 16px;">
                            <div style="color: #B91C1C; font-weight: 700; font-size: 16px; margin-bottom: 8px;">Deal Breaker</div>
                            <ul style="list-style-type: disc; padding-left: 20px; color: #991B1B; font-size: 14px;">
                                @foreach($compatibility['conflicts'] as $conflict)
                                    <li>{{ $conflict }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div style="text-align: center; margin-bottom: 24px;">
                            <div style="font-size: 48px; font-weight: 800; color: var(--dwello-primary);">
                                {{ $compatibility['score'] }}%
                            </div>
                            <div style="color: var(--gray-600); font-size: 14px;">Match Score</div>
                        </div>

                        <div style="width: 100%; background: var(--gray-200); height: 8px; border-radius: 4px; margin-bottom: 24px; overflow: hidden;">
                            <div style="width: {{ $compatibility['score'] }}%; background: var(--dwello-primary); height: 100%;"></div>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <h4 style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-500); font-weight: 600; margin-bottom: 12px;">
                                Why this match?
                            </h4>
                            <ul style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($compatibility['reasons'] as $reason)
                                    <li class="flex items-start gap-2" style="font-size: 14px; color: var(--gray-700);">
                                        @if($reason['type'] === 'positive')
                                            <span style="color: #10B981;">✅</span>
                                        @else
                                            <span style="color: #F59E0B;">⚠️</span>
                                        @endif
                                        <span>{{ $reason['text'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @if(auth()->check())
                        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--gray-200); text-align: center;">
                            <a href="{{ route('profile.edit') }}" style="color: var(--dwello-primary); font-size: 14px; text-decoration: none; font-weight: 500;">
                                Update my preferences
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <p style="color: var(--gray-600); text-align: center;">
                        <a href="{{ route('login') }}" style="color: var(--dwello-primary); text-decoration: underline;">Log in</a> 
                        to see your compatibility with {{ $roommateProfile->display_name }}.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
