@extends('layouts.dwello')

@section('title', 'Edit Property - Dwello')

@section('content')
<div class="container mx-auto px-4 py-8 md:px-6">
    <div style="max-width: 800px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 24px;">
        <h2 style="font-size: 24px; font-family: 'Poppins', sans-serif; font-weight: 600; margin-bottom: 16px;">
            Edit Property
        </h2>
        <p style="color: var(--gray-600); margin-bottom: 24px;">
            Update the details of your property listing.
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

        <form method="POST" action="{{ route('properties.update', $property) }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Title</label>
                <input class="input @error('title') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                       name="title" value="{{ old('title', $property->title) }}" required>
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Description</label>
                <textarea class="input @error('description') border-red-500 @enderror" style="width:100%; border-radius:12px; min-height:100px;"
                          name="description">{{ old('description', $property->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Add More Photos (Optional)</label>
                <input type="file" name="photos[]" multiple accept="image/*" class="input @error('photos') border-red-500 @enderror @error('photos.*') border-red-500 @enderror" style="width:100%; border-radius:12px; padding: 10px;">
                <p style="font-size: 12px; color: var(--gray-500); margin-top: 4px;">Existing photos remain. New photos will be added.</p>
                @error('photos')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">City</label>
                    <select class="input @error('city') border-red-500 @enderror" id="city" style="width:100%; border-radius:12px;" name="city" required>
                        <option value="">Select City</option>
                        @foreach(config('cities') as $city)
                            <option value="{{ $city }}" {{ old('city', $property->city) == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    @error('city')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Address</label>
                    <input class="input @error('address') border-red-500 @enderror" id="address" style="width:100%; border-radius:12px;"
                           name="address" value="{{ old('address', $property->address) }}" required>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Monthly Rent (LKR)</label>
                    <input type="number" step="100" min="0" class="input @error('monthly_rent') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                           name="monthly_rent" value="{{ old('monthly_rent', $property->monthly_rent) }}" required>
                    @error('monthly_rent')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Bedrooms</label>
                    <input type="number" min="0" class="input @error('bedrooms') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                           name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" required>
                    @error('bedrooms')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Bathrooms</label>
                    <input type="number" min="0" class="input @error('bathrooms') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                           name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}" required>
                    @error('bathrooms')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Property Type</label>
                    <select class="input @error('property_type') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                            name="property_type" required>
                        <option value="room" {{ old('property_type', $property->property_type) == 'room' ? 'selected' : '' }}>Room</option>
                        <option value="apartment" {{ old('property_type', $property->property_type) == 'apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="house" {{ old('property_type', $property->property_type) == 'house' ? 'selected' : '' }}>House</option>
                    </select>
                    @error('property_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Available From</label>
                    <input type="date" class="input @error('available_from') border-red-500 @enderror" style="width:100%; border-radius:12px;"
                           name="available_from" value="{{ old('available_from', $property->available_from ? \Carbon\Carbon::parse($property->available_from)->format('Y-m-d') : '') }}">
                    @error('available_from')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="margin-bottom: 24px;">
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $property->latitude) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $property->longitude) }}">
            </div>
            
            <div style="margin-bottom: 24px;">
                <label style="display:block; font-size: 14px; font-weight:500; margin-bottom:6px;">Location on Map (Click to set)</label>
                <div id="map" style="height: 300px; width: 100%; border-radius: 12px; z-index: 1;"></div>
            </div>

            <div class="flex justify-end" style="gap: 12px;">
                <a href="{{ route('properties.show', $property) }}" class="btn btn-outline">Cancel</a>
                <button class="btn btn-primary" type="submit">Update Listing</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('form-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (['e', 'E', '+', '-'].includes(e.key)) e.preventDefault();
        });
    });

    const cityInput = document.getElementById('city');
    const addressInput = document.getElementById('address');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    // Default or Existing
    let mapCenter = [6.9271, 79.8612]; 
    let mapZoom = 13;
    let initialLat = parseFloat(latInput.value);
    let initialLng = parseFloat(lngInput.value);

    if (!isNaN(initialLat) && !isNaN(initialLng)) {
        mapCenter = [initialLat, initialLng];
    }

    var map = L.map('map').setView(mapCenter, mapZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var marker;

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

    function updateInputs(lat, lng) {
        latInput.value = lat;
        lngInput.value = lng;
    }

    if (!isNaN(initialLat) && !isNaN(initialLng)) {
        setMarker(initialLat, initialLng);
    }

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
                setMarker(parseFloat(data[0].lat), parseFloat(data[0].lon));
            }
        } catch (error) {
            console.error('Geocoding failed:', error);
        }
    }
    
    cityInput.addEventListener('change', fetchCoordinates);
    addressInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchCoordinates, 1500);
    });
});
</script>
@endpush
