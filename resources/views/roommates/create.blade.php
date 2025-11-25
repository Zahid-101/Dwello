@extends('layouts.dwello')

@section('title', 'My Roommate Profile - Dwello')

@section('content')
<div class="container" style="padding: 32px 24px;">
    <div style="max-width: 800px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 24px;">
        <h2 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: 600; margin-bottom: 16px;">
            My Roommate Profile
        </h2>
        <p style="color: var(--gray-600); margin-bottom: 24px;">
            Tell others about yourself so we can match you with compatible flatmates.
        </p>

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

            <div class="flex" style="gap: 16px; margin-bottom: 16px;">
                <label class="flex items-center" style="gap: 8px;">
                        <input type="checkbox" name="is_smoker"
                            {{ old('is_smoker', $profile->is_smoker ?? false) ? 'checked' : '' }}>
                        <span style="font-size: 14px;">I smoke</span>
                    </label>
                    <label class="flex items-center" style="gap: 8px;">
                        <input type="checkbox" name="has_pets"
                            {{ old('has_pets', $profile->has_pets ?? false) ? 'checked' : '' }}>
                        <span style="font-size: 14px;">I have pets</span>
                </label>
            </div>

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