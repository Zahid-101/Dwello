@extends('layouts.dwello')

@section('title', 'My Roommate Profile - Dwello')

@section('content')
<div class="container" style="padding: 32px 24px;">
    <div style="max-width: 800px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 24px;">
        <h2 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: 600; margin-bottom: 16px;">
            My Roommate Profile
        </h2>
            Tell others about yourself so we can match you with compatible flatmates.
        </p>

        {{-- Quick Actions Toolbar --}}
        <div class="flex gap-4 mb-6">
            <a href="{{ route('messages.index') }}" class="flex-1 bg-blue-50 text-blue-600 rounded-xl p-4 flex items-center justify-center gap-2 hover:bg-blue-100 transition font-medium border border-blue-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                Inbox
            </a>
            <a href="{{ route('favorites.index') }}" class="flex-1 bg-pink-50 text-pink-600 rounded-xl p-4 flex items-center justify-center gap-2 hover:bg-pink-100 transition font-medium border border-pink-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                Favorites
            </a>
        </div>

        @if ($errors->any())
            <div style="background: #FEE2E2; color: #991B1B; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-size: 14px;">
                <ul style="margin-left: 16px; list-style: disc;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('roommate-profiles.store') }}">
            @csrf

            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Display Name</label>
                <input class="input" style="width:100%; border-radius:12px;"
                       name="display_name"
                       value="{{ old('display_name', $profile->display_name ?? auth()->user()->name) }}"
                       required>
            </div>

            <div class="grid grid-3 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Age</label>
                    <input type="number" class="input" style="width:100%; border-radius:12px;"
                           name="age" value="{{ old('age', $profile->age ?? null) }}">
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Gender</label>
                    @php $gender = old('gender', $profile->gender ?? null); @endphp
                    <select class="input" style="width:100%; border-radius:12px;" name="gender">
                        <option value="">Prefer not to say</option>
                        <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ $gender === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Move-in Date</label>
                    <input type="date" class="input" style="width:100%; border-radius:12px;"
                           name="move_in_date"
                           value="{{ old('move_in_date', optional($profile->move_in_date ?? null)->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="grid grid-2 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Preferred City</label>
                    <input class="input" style="width:100%; border-radius:12px;"
                           name="preferred_city"
                           value="{{ old('preferred_city', $profile->preferred_city ?? null) }}">
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Preferred Area / Location</label>
                    <input class="input" style="width:100%; border-radius:12px;"
                           name="preferred_location"
                           value="{{ old('preferred_location', $profile->preferred_location ?? null) }}">
                </div>
            </div>

            <div class="grid grid-2 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Budget Min (LKR)</label>
                    <input type="number" step="0.01" class="input" style="width:100%; border-radius:12px;"
                           name="budget_min"
                           value="{{ old('budget_min', $profile->budget_min ?? null) }}">
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Budget Max (LKR)</label>
                    <input type="number" step="0.01" class="input" style="width:100%; border-radius:12px;"
                           name="budget_max"
                           value="{{ old('budget_max', $profile->budget_max ?? null) }}">
                </div>
            </div>

            <div class="flex" style="gap: 16px; margin-bottom: 24px;">
                <label class="flex items-center" style="gap: 8px;">
                    <input type="checkbox" name="is_smoker" value="1"
                           {{ old('is_smoker', $profile->is_smoker ?? false) ? 'checked' : '' }}>
                    <span style="font-size: 14px;">I smoke</span>
                </label>
                <label class="flex items-center" style="gap: 8px;">
                    <input type="checkbox" name="has_pets" value="1"
                           {{ old('has_pets', $profile->has_pets ?? false) ? 'checked' : '' }}>
                    <span style="font-size: 14px;">I have pets</span>
                </label>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--gray-200); margin-bottom: 24px;">
            
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--gray-900);">Compatibility Preferences</h3>
            
            <div style="margin-bottom: 24px;">
                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--gray-700);">Deal Breakers & Preferences</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="pref_no_smoker" value="1" {{ old('pref_no_smoker', $profile->pref_no_smoker ?? false) ? 'checked' : '' }}>
                        <span style="font-size: 14px; color: var(--gray-700);">Prefer Non-smokers</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="pref_pets_ok" value="1" {{ old('pref_pets_ok', $profile->pref_pets_ok ?? true) ? 'checked' : '' }}>
                        <span style="font-size: 14px; color: var(--gray-700);">Pets OK</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="pref_same_gender_only" value="1" {{ old('pref_same_gender_only', $profile->pref_same_gender_only ?? false) ? 'checked' : '' }}>
                        <span style="font-size: 14px; color: var(--gray-700);">Same Gender Only</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="pref_visitors_ok" value="1" {{ old('pref_visitors_ok', $profile->pref_visitors_ok ?? true) ? 'checked' : '' }}>
                        <span style="font-size: 14px; color: var(--gray-700);">Visitors Allowed</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="pref_substance_free_required" value="1" {{ old('pref_substance_free_required', $profile->pref_substance_free_required ?? false) ? 'checked' : '' }}>
                        <span style="font-size: 14px; color: var(--gray-700);">Substance-free Home Required</span>
                    </label>
                     <label class="flex items-center gap-2">
                        <input type="checkbox" name="uses_substances" value="1" {{ old('uses_substances', $profile->uses_substances ?? false) ? 'checked' : '' }}>
                        <span style="font-size: 14px; color: var(--gray-700);">I use substances (alcohol/etc)</span>
                    </label>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--gray-700);">Lifestyle (1-5 Scale)</h4>
                <p style="font-size: 12px; color: var(--gray-500); margin-bottom: 16px;">1 = Low/Quiet/Messy, 5 = High/Loud/My Cleanest Self</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach(['cleanliness' => 'Cleanliness', 'noise_tolerance' => 'Noise Tolerance', 'sleep_schedule' => 'Sleep Schedule (Early vs Late)', 'study_focus' => 'Study Focus', 'social_level' => 'Social Level'] as $field => $label)
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">{{ $label }}</label>
                        <select name="{{ $field }}" class="input" style="width:100%; border-radius:12px;">
                            <option value="">Select Level</option>
                            @foreach(range(1, 5) as $val)
                                <option value="{{ $val }}" {{ old($field, $profile->$field ?? '') == $val ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                 <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--gray-700);">Additional Details</h4>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Schedule Type</label>
                        <select name="schedule_type" class="input" style="width:100%; border-radius:12px;">
                            <option value="">Select Schedule</option>
                            <option value="morning" {{ old('schedule_type', $profile->schedule_type ?? '') == 'morning' ? 'selected' : '' }}>Morning Person</option>
                            <option value="night" {{ old('schedule_type', $profile->schedule_type ?? '') == 'night' ? 'selected' : '' }}>Night Owl</option>
                            <option value="mixed" {{ old('schedule_type', $profile->schedule_type ?? '') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                        </select>
                     </div>
                     <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Occupation / Field</label>
                        <input type="text" name="occupation_field" class="input" style="width:100%; border-radius:12px;"
                               value="{{ old('occupation_field', $profile->occupation_field ?? '') }}" placeholder="e.g. Student, Engineer, Artist">
                     </div>
                 </div>
            </div>
            
            <hr style="border: 0; border-top: 1px solid var(--gray-200); margin-bottom: 24px;">

            <div style="margin-bottom: 24px;">
                <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">About You</label>
                <textarea class="input" style="width:100%; border-radius:12px; min-height:120px;"
                          name="bio">{{ old('bio', $profile->bio ?? null) }}</textarea>
            </div>

            <div class="flex justify-end" style="gap: 12px;">
                <a href="{{ route('roommates.index') }}" class="btn btn-outline">Cancel</a>
                <button class="btn btn-primary" type="submit">Save Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection