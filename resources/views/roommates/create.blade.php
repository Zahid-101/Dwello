@extends('layouts.dwello')

@section('title', 'My Roommate Profile - Dwello')

@section('content')
    <div class="container mx-auto px-4 py-8 md:px-6">
        <div
            style="max-width: 800px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 24px;">
            <h2 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: 600; margin-bottom: 16px;">
                My Roommate Profile
            </h2>
            Tell others about yourself so we can match you with compatible flatmates.
            </p>

            {{-- Quick Actions Toolbar --}}
            <div class="flex gap-4 mb-6">
                <a href="{{ route('messages.index') }}"
                    class="flex-1 bg-blue-50 text-blue-600 rounded-xl p-4 flex items-center justify-center gap-2 hover:bg-blue-100 transition font-medium border border-blue-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                        </path>
                    </svg>
                    Inbox
                </a>
                <a href="{{ route('favorites.index') }}"
                    class="flex-1 bg-pink-50 text-pink-600 rounded-xl p-4 flex items-center justify-center gap-2 hover:bg-pink-100 transition font-medium border border-pink-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                        </path>
                    </svg>
                    Favorites
                </a>
            </div>

            @if ($errors->any())
                <div
                    style="background: #FEE2E2; color: #991B1B; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-size: 14px;">
                    <ul style="margin-left: 16px; list-style: disc;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('roommate-profiles.store') }}" enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom: 24px;">
                    <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--gray-700);">Profile
                        Picture</h4>
                    <div
                        class="relative w-16 h-16 rounded-full overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center">
                        <?php $profilePhotoUrl = auth()->user()->profile_photo_path ? Storage::url(auth()->user()->profile_photo_path) : null; ?>
                        <img id="profile-preview" src="{{ $profilePhotoUrl }}" alt="Current Profile"
                            class="{{ $profilePhotoUrl ? '' : 'hidden' }} w-full h-full object-cover">
                        <span id="profile-placeholder" class="{{ $profilePhotoUrl ? 'hidden' : '' }} text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="profile_photo" accept="image/*" class="block w-full text-sm text-slate-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-violet-50 file:text-violet-700
                                  hover:file:bg-violet-100
                                  cursor-pointer
                                " onchange="previewImage(this)" />
                    </div>
                    @error('profile_photo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Display Name</label>
                    <input class="input @error('display_name') border-red-500 @enderror"
                        style="width:100%; border-radius:12px;" name="display_name"
                        value="{{ old('display_name', $profile->display_name ?? auth()->user()->name) }}" required>
                    @error('display_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6" style="margin-bottom: 16px;">
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Age</label>
                        <input type="number" min="16" max="100" class="input @error('age') border-red-500 @enderror"
                            style="width:100%; border-radius:12px;" name="age"
                            value="{{ old('age', $profile->age ?? null) }}">
                        @error('age')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Gender</label>
                        @php $gender = old('gender', $profile->gender ?? null); @endphp
                        <select class="input @error('gender') border-red-500 @enderror"
                            style="width:100%; border-radius:12px;" name="gender">
                            <option value="">Prefer not to say</option>
                            <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ $gender === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Move-in
                            Date</label>
                        <input type="date" class="input @error('move_in_date') border-red-500 @enderror"
                            style="width:100%; border-radius:12px;" name="move_in_date"
                            value="{{ old('move_in_date', optional($profile->move_in_date ?? null)->format('Y-m-d')) }}">
                        @error('move_in_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="margin-bottom: 16px;">
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Preferred
                            City</label>
                        <select class="input @error('preferred_city') border-red-500 @enderror"
                            style="width:100%; border-radius:12px;" name="preferred_city">
                            <option value="">Select Preferred City</option>
                            @foreach(config('cities') as $city)
                                <option value="{{ $city }}" {{ old('preferred_city', $profile->preferred_city ?? '') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                        @error('preferred_city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Preferred Area /
                            Location</label>
                        <input class="input @error('preferred_location') border-red-500 @enderror"
                            style="width:100%; border-radius:12px;" name="preferred_location"
                            value="{{ old('preferred_location', $profile->preferred_location ?? null) }}">
                        @error('preferred_location')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Looking
                            For</label>
                        <select class="input @error('preferred_property_type') border-red-500 @enderror"
                            style="width:100%; border-radius:12px;" name="preferred_property_type">
                            <option value="">Any</option>
                            <option value="room" {{ old('preferred_property_type', $profile->preferred_property_type ?? '') == 'room' ? 'selected' : '' }}>Room</option>
                            <option value="apartment" {{ old('preferred_property_type', $profile->preferred_property_type ?? '') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="house" {{ old('preferred_property_type', $profile->preferred_property_type ?? '') == 'house' ? 'selected' : '' }}>House</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="margin-bottom: 16px;">
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Budget Min
                            (LKR)</label>
                        <input type="number" step="100" min="0" class="input @error('budget_min') border-red-500 @enderror"
                            style="width:100%; border-radius:12px;" name="budget_min"
                            value="{{ old('budget_min', $profile->budget_min ?? null) }}">
                        @error('budget_min')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Budget Max
                            (LKR)</label>
                        <input type="number" step="100" min="0" class="input @error('budget_max') border-red-500 @enderror"
                            style="width:100%; border-radius:12px;" name="budget_max"
                            value="{{ old('budget_max', $profile->budget_max ?? null) }}">
                        @error('budget_max')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex" style="gap: 16px; margin-bottom: 24px;">
                    <label class="flex items-center" style="gap: 8px;">
                        <input type="checkbox" name="is_smoker" value="1" {{ old('is_smoker', $profile->is_smoker ?? false) ? 'checked' : '' }}>
                        <span style="font-size: 14px;">I smoke</span>
                    </label>
                    <label class="flex items-center" style="gap: 8px;">
                        <input type="checkbox" name="has_pets" value="1" {{ old('has_pets', $profile->has_pets ?? false) ? 'checked' : '' }}>
                        <span style="font-size: 14px;">I have pets</span>
                    </label>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--gray-200); margin-bottom: 24px;">

                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--gray-900);">Compatibility
                    Preferences</h3>

                <div style="margin-bottom: 24px;">
                    <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--gray-700);">Deal
                        Breakers & Preferences</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--gray-700);">Lifestyle
                        Preferences</h4>
                    <p style="font-size: 12px; color: var(--gray-500); margin-bottom: 16px;">Select the option that best
                        describes you.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Noise Tolerance --}}
                        <div>
                            <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Noise
                                Tolerance</label>
                            <select name="noise_tolerance" class="input" style="width:100%; border-radius:12px;">
                                <option value="">Select Level</option>
                                <option value="1" {{ old('noise_tolerance', $profile->noise_tolerance ?? '') == 1 ? 'selected' : '' }}>Quiet</option>
                                <option value="3" {{ old('noise_tolerance', $profile->noise_tolerance ?? '') == 3 ? 'selected' : '' }}>Moderate</option>
                                <option value="5" {{ old('noise_tolerance', $profile->noise_tolerance ?? '') == 5 ? 'selected' : '' }}>Loud / OK with noise</option>
                            </select>
                        </div>

                        {{-- Sleep Schedule --}}
                        <div>
                            <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Sleep
                                Schedule</label>
                            <select name="sleep_schedule" class="input" style="width:100%; border-radius:12px;">
                                <option value="">Select Level</option>
                                <option value="1" {{ old('sleep_schedule', $profile->sleep_schedule ?? '') == 1 ? 'selected' : '' }}>Early sleeper</option>
                                <option value="3" {{ old('sleep_schedule', $profile->sleep_schedule ?? '') == 3 ? 'selected' : '' }}>Flexible</option>
                                <option value="5" {{ old('sleep_schedule', $profile->sleep_schedule ?? '') == 5 ? 'selected' : '' }}>Night owl</option>
                            </select>
                        </div>

                        {{-- Study Focus --}}
                        <div>
                            <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Study
                                Focus</label>
                            <select name="study_focus" class="input" style="width:100%; border-radius:12px;">
                                <option value="">Select Level</option>
                                <option value="1" {{ old('study_focus', $profile->study_focus ?? '') == 1 ? 'selected' : '' }}>Relaxed</option>
                                <option value="3" {{ old('study_focus', $profile->study_focus ?? '') == 3 ? 'selected' : '' }}>Moderate</option>
                                <option value="5" {{ old('study_focus', $profile->study_focus ?? '') == 5 ? 'selected' : '' }}>Focused</option>
                            </select>
                        </div>

                        {{-- Social Level --}}
                        <div>
                            <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Social
                                Level</label>
                            <select name="social_level" class="input" style="width:100%; border-radius:12px;">
                                <option value="">Select Level</option>
                                <option value="1" {{ old('social_level', $profile->social_level ?? '') == 1 ? 'selected' : '' }}>Private</option>
                                <option value="3" {{ old('social_level', $profile->social_level ?? '') == 3 ? 'selected' : '' }}>Balanced</option>
                                <option value="5" {{ old('social_level', $profile->social_level ?? '') == 5 ? 'selected' : '' }}>Social</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--gray-700);">Additional
                        Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Schedule
                                Type</label>
                            <select name="schedule_type" class="input" style="width:100%; border-radius:12px;">
                                <option value="">Select Schedule</option>
                                <option value="morning" {{ old('schedule_type', $profile->schedule_type ?? '') == 'morning' ? 'selected' : '' }}>Morning Person</option>
                                <option value="night" {{ old('schedule_type', $profile->schedule_type ?? '') == 'night' ? 'selected' : '' }}>Night Owl</option>
                                <option value="mixed" {{ old('schedule_type', $profile->schedule_type ?? '') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Occupation /
                                Field</label>
                            <input type="text" name="occupation_field" class="input" style="width:100%; border-radius:12px;"
                                value="{{ old('occupation_field', $profile->occupation_field ?? '') }}"
                                placeholder="e.g. Student, Engineer, Artist">
                        </div>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--gray-200); margin-bottom: 24px;">

                <div style="margin-bottom: 24px;">
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">About You</label>
                    <textarea class="input" style="width:100%; border-radius:12px; min-height:120px;"
                        name="bio">{{ old('bio', $profile->bio ?? null) }}</textarea>
                </div>

                <div class="flex justify-between items-center" style="gap: 12px;">
                    <a href="{{ route('profile.edit') }}"
                        class="btn btn-outline border-red-500 text-red-500 hover:bg-red-50" style="margin-right: auto;">
                        Account Settings / Delete Account
                    </a>
                    <div class="flex gap-3">
                        <a href="{{ route('roommates.index') }}" class="btn btn-outline">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save Profile</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(input) {
            const preview = document.getElementById('profile-preview');
            const placeholder = document.getElementById('profile-placeholder');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Block 'e', 'E', '+', '-' from number inputs
            const numberInputs = document.querySelectorAll('input[type="number"]');
            numberInputs.forEach(input => {
                input.addEventListener('keydown', function (e) {
                    if (['e', 'E', '+', '-'].includes(e.key)) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endpush