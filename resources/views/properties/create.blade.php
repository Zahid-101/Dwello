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

        <form method="POST" action="{{ route('properties.store') }}" class="space-y-4" enctype="multipart/form-data">
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

            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Photos (Max 6)</label>
                <input type="file" name="photos[]" multiple accept="image/*" class="input" style="width:100%; border-radius:12px; padding: 10px;">
                <p style="font-size: 12px; color: var(--gray-500); margin-top: 4px;">Supported formats: JPEG, PNG, WEBP. Max 2MB each.</p>
            </div>

            <div class="grid grid-2 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">City</label>
                    <select class="input" id="city" style="width:100%; border-radius:12px;" name="city" required>
                        <option value="">Select City</option>
                        @foreach(config('cities') as $city)
                            <option value="{{ $city }}" {{ old('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Address</label>
                    <input class="input" id="address" style="width:100%; border-radius:12px;"
                           name="address" value="{{ old('address') }}" required>
                </div>
            </div>

            <div class="grid grid-3 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Monthly Rent (LKR)</label>
                    <input type="number" step="100" min="0" class="input" style="width:100%; border-radius:12px;"
                           name="monthly_rent" value="{{ old('monthly_rent') }}" required>
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Bedrooms</label>
                    <input type="number" min="0" class="input" style="width:100%; border-radius:12px;"
                           name="bedrooms" value="{{ old('bedrooms',1) }}" required>
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Bathrooms</label>
                    <input type="number" min="0" class="input" style="width:100%; border-radius:12px;"
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
            
            <div style="margin-bottom: 24px;">
                <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Location on Map (Click to set)</label>
                <div id="map" style="height: 300px; width: 100%; border-radius: 12px; z-index: 1;"></div>
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
@push('form-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Block 'e', 'E', '+', '-' from number inputs
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (['e', 'E', '+', '-'].includes(e.key)) {
                e.preventDefault();
            }
        });
    });

    console.log('Starting map & geocoding script...');

    const cityInput = document.getElementById('city');
    const addressInput = document.getElementById('address');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    // Default Colombo
    let mapCenter = [6.9271, 79.8612]; 
    let mapZoom = 13;

    // Check if we have existing values (old input) to center map
    if (latInput.value && lngInput.value) {
        mapCenter = [parseFloat(latInput.value), parseFloat(lngInput.value)];
         // visual map center logic handled below
    }

    // Initialize Map
    var map = L.map('map').setView(mapCenter, mapZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var marker;

    // Function to set marker
    function setMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {draggable: true}).addTo(map);
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateInputs(pos.lat, pos.lng);
            });
        }
        map.setView([lat, lng], mapZoom);
        updateInputs(lat, lng);
    }

    // Function to update hidden inputs
    function updateInputs(lat, lng) {
        latInput.value = lat;
        lngInput.value = lng;
    }

    // If we had initial values, set marker
    if (latInput.value && lngInput.value) {
        setMarker(parseFloat(latInput.value), parseFloat(lngInput.value));
    }

    // Map click listener
    map.on('click', function(e) {
        setMarker(e.latlng.lat, e.latlng.lng);
    });
    
    // Geocoding Logic
    let debounceTimer = null;
    
    async function fetchCoordinates() {
        const city = cityInput.value.trim();
        const address = addressInput.value.trim();
        
        if (city.length < 2) return; 
        
        const query = `${address ? address + ', ' : ''}${city}, Sri Lanka`;
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`;
        
        try {
            const response = await fetch(url);
            if (!response.ok) return;
            
            const data = await response.json();
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);
                setMarker(lat, lon);
            }
        } catch (error) {
            console.error('Geocoding failed:', error);
        }
    }
    
    // Listeners for geocoding
    cityInput.addEventListener('change', fetchCoordinates); // Changed input to change for select
    
    addressInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchCoordinates, 1500);
    });

    // Handle form submit just in case (optional, validation handled by required attributes)
});
</script>
@endpush
