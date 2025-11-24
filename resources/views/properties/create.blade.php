@extends('layouts.dwello')

@section('title', 'List a Property - Dwello')

@section('content')
<div class="container" style="padding: 32px 24px;">
    <div style="max-width: 800px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 24px;">
        <h2 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: 600; margin-bottom: 16px;">
            List a room or property
        </h2>
        <p style="color: var(--gray-600); margin-bottom: 24px;">
            Fill in the details below. Verified listings perform better in search.
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

        <form method="POST" action="{{ route('properties.store') }}" class="space-y-4">
            @csrf

            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Title</label>
                <input class="input" style="width:100%; border-radius:12px;"
                       name="title" value="{{ old('title') }}" required>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Description</label>
                <textarea class="input" style="width:100%; border-radius:12px; min-height:100px;"
                          name="description">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-2 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">City</label>
                    <input class="input" style="width:100%; border-radius:12px;"
                           name="city" value="{{ old('city') }}" required>
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Address</label>
                    <input class="input" style="width:100%; border-radius:12px;"
                           name="address" value="{{ old('address') }}" required>
                </div>
            </div>

            <div class="grid grid-3 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Monthly Rent (LKR)</label>
                    <input type="number" step="0.01" class="input" style="width:100%; border-radius:12px;"
                           name="monthly_rent" value="{{ old('monthly_rent') }}" required>
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Bedrooms</label>
                    <input type="number" class="input" style="width:100%; border-radius:12px;"
                           name="bedrooms" value="{{ old('bedrooms',1) }}" required>
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Bathrooms</label>
                    <input type="number" class="input" style="width:100%; border-radius:12px;"
                           name="bathrooms" value="{{ old('bathrooms',1) }}" required>
                </div>
            </div>

            <div class="grid grid-2 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Property Type</label>
                    <select class="input" style="width:100%; border-radius:12px;"
                            name="property_type" required>
                        <option value="room" {{ old('property_type') == 'room' ? 'selected' : '' }}>Room</option>
                        <option value="apartment" {{ old('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="house" {{ old('property_type') == 'house' ? 'selected' : '' }}>House</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Available From</label>
                    <input type="date" class="input" style="width:100%; border-radius:12px;"
                           name="available_from" value="{{ old('available_from') }}">
                </div>
            </div>

            <div class="grid grid-2 gap-6" style="margin-bottom: 24px;">
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                </div>

            <div class="flex justify-end" style="gap: 12px;">
                <a href="{{ route('properties.index') }}" class="btn btn-outline">Cancel</a>
                <button class="btn btn-primary" type="submit">Save Listing</button>
            </div>
        </form>
    </div>
</div>
@endsection
{{--Automatic loaction for our lat and long from address given--}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cityInput = document.getElementById('city');
        const addressInput = document.getElementById('address');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        async function fetchCoordinates() {
            const city = cityInput.value;
            const address = addressInput.value;

            if (city.length < 2 || address.length < 5) return;

            const query = `${address}, ${city}`;
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`;

            try {
                // REMOVED: latInput.placeholder = "Searching..."; (User can't see this anyway)

                const response = await fetch(url, {
                    headers: { 'User-Agent': 'Dwello-Student-Project' }
                });
                const data = await response.json();

                if (data && data.length > 0) {
                    latInput.value = data[0].lat;
                    lngInput.value = data[0].lon;
                    console.log("Location found:", data[0].lat, data[0].lon); // Optional: for debugging
                }
            } catch (error) {
                console.error('Geocoding error:', error);
            }
        }

        cityInput.addEventListener('blur', fetchCoordinates);
        addressInput.addEventListener('blur', fetchCoordinates);
    });
</script>
@endpush