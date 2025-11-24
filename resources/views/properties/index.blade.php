{{-- resources/views/properties/index.blade.php --}}
@extends('layouts.dwello')

@section('title', 'Search Rooms - Dwello')

@section('content')
    <!-- Filter pills bar -->
    <div style="background: white; border-bottom: 1px solid var(--gray-200); padding: 16px 0;">
        <div class="container">
            <div class="flex items-center" style="gap: 16px; overflow-x: auto;">
                <span style="color: var(--gray-600); font-size: 14px; white-space: nowrap;">Filters:</span>
                <div class="flex" style="gap: 8px;">
                    @if(request('min_rent') || request('max_rent'))
                        <span style="background: var(--dwello-primary); color: white; padding: 4px 12px; border-radius: 12px; font-size: 14px; white-space: nowrap;">
                            LKR {{ request('min_rent') ?? 0 }} - {{ request('max_rent') ?? '100000+' }}
                        </span>
                    @endif

                    @if(request('city'))
                        <span class="badge badge-lifestyle" style="white-space: nowrap;">{{ request('city') }}</span>
                    @endif

                    @if(request('type'))
                        <span class="badge badge-lifestyle" style="white-space: nowrap;">
                            {{ ucfirst(request('type')) }}
                        </span>
                    @endif

                    <a href="{{ route('properties.index') }}"
                       style="color: var(--dwello-primary); font-size: 14px; background: none; border: none; cursor: pointer; text-decoration:none;">
                        Clear all
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Filters / Results / Map -->
    <div style="display: flex; height: calc(100vh - 160px);">
        {{-- Left pane: Filters --}}
        <div style="width: 320px; background: white; box-shadow: 4px 0 6px -1px rgba(0, 0, 0, 0.1); overflow-y: auto;">
            <div style="padding: 24px;">
                <h2 style="font-size: 20px; font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gray-900); margin-bottom: 24px;">
                    Filters
                </h2>

                <form method="GET" action="{{ route('properties.index') }}">
                    {{-- Budget --}}
                    <div style="margin-bottom: 24px;">
                        <h3 style="font-weight: 500; color: var(--gray-900); margin-bottom: 8px;">Budget (LKR)</h3>
                        <div class="flex" style="gap: 8px;">
                            <input class="input" type="number" name="min_rent" placeholder="Min"
                                   value="{{ request('min_rent') }}" style="border-radius: 12px; width: 50%;">
                            <input class="input" type="number" name="max_rent" placeholder="Max"
                                   value="{{ request('max_rent') }}" style="border-radius: 12px; width: 50%;">
                        </div>
                    </div>

                    {{-- City --}}
                    <div style="margin-bottom: 24px;">
                        <h3 style="font-weight: 500; color: var(--gray-900); margin-bottom: 8px;">City</h3>
                        <input class="input" type="text" name="city" placeholder="e.g. Colombo"
                               value="{{ request('city') }}" style="border-radius: 12px; width: 100%;">
                    </div>

                    {{-- Text search --}}
                    <div style="margin-bottom: 24px;">
                        <h3 style="font-weight: 500; color: var(--gray-900); margin-bottom: 8px;">Search</h3>
                        <input class="input" type="text" name="q" placeholder="Title, area, address"
                               value="{{ request('q') }}" style="border-radius: 12px; width: 100%;">
                    </div>

                    {{-- Type --}}
                    <div style="margin-bottom: 24px;">
                        <h3 style="font-weight: 500; color: var(--gray-900); margin-bottom: 8px;">Type</h3>
                        <select name="type" class="input" style="border-radius: 12px; width: 100%;">
                            <option value="">Any type</option>
                            <option value="room" {{ request('type') === 'room' ? 'selected' : '' }}>Room</option>
                            <option value="apartment" {{ request('type') === 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="house" {{ request('type') === 'house' ? 'selected' : '' }}>House</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 16px;">
                        Apply Filters
                    </button>
                </form>
            </div>
        </div>

        {{-- Right pane: results + map --}}
        <div style="flex: 1; display: flex;">
            {{-- Results --}}
            <div style="flex: 1; overflow-y: auto;">
                <div style="padding: 24px;">
                    <div class="flex items-center justify-between" style="margin-bottom: 8px;">
                        <h2 style="font-size: 20px; font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gray-900);">
                            {{ $properties->total() }} rooms found
                        </h2>
                    </div>
                    <p style="color: var(--gray-600); font-size: 14px; margin-bottom: 16px;">
                        Showing {{ $properties->count() }} result(s)
                        @if(request()->hasAny(['q','city','min_rent','max_rent','type']))
                            for your filters.
                        @endif
                    </p>

                    {{-- Listings --}}
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        @forelse ($properties as $property)
                            <div style="background: white; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); overflow: hidden;">
                                <div style="display: flex;">
                                    {{-- Image placeholder --}}
                                    <div style="width: 280px; height: 200px; position: relative;">
                                        <img
                                            src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=300&h=200&fit=crop"
                                            alt="{{ $property->title }}"
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                        >
                                        <div style="position: absolute; top: 12px; left: 12px;">
                                            <span class="badge badge-verified">Verified</span>
                                        </div>
                                    </div>

                                    {{-- Content --}}
                                    <div style="flex: 1; padding: 24px;">
                                        <div class="flex justify-between items-start" style="margin-bottom: 8px;">
                                            <h3 style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 18px; color: var(--gray-900);">
                                                {{ $property->title }}
                                            </h3>
                                            <span style="font-size: 24px; font-weight: bold; color: var(--dwello-primary);">
                                                LKR {{ number_format($property->monthly_rent, 0) }}
                                                <span style="font-size: 14px; color: var(--gray-500); font-weight: normal;">/mo</span>
                                            </span>
                                        </div>
                                        <p style="color: var(--gray-600); font-size: 14px; margin-bottom: 8px;">
                                            {{ $property->city }} • {{ $property->address }}
                                        </p>
                                        @if($property->available_from)
                                            <p style="color: var(--gray-500); font-size: 13px; margin-bottom: 8px;">
                                                Available from {{ \Carbon\Carbon::parse($property->available_from)->toFormattedDateString() }}
                                            </p>
                                        @endif
                                        <p style="color: var(--gray-700); margin-bottom: 12px;">
                                            {{ \Illuminate\Support\Str::limit($property->description, 200) }}
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex" style="gap: 8px;">
                                                <span class="badge badge-lifestyle">
                                                    {{ ucfirst($property->property_type) }}
                                                </span>
                                                <span class="badge badge-lifestyle">
                                                    {{ $property->bedrooms }} bed • {{ $property->bathrooms }} bath
                                                </span>
                                            </div>
                                            <button style="color: var(--dwello-primary); font-weight: 500; background: none; border: none; cursor: pointer;">
                                                View Details →
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p style="color: var(--gray-600);">No properties match your filters.</p>
                        @endforelse
                    </div>

                    <div style="margin-top: 24px;">
                        {{ $properties->links() }}
                    </div>
                </div>
            </div>

            {{-- Map --}}
            <div style="width: 400px; background: var(--gray-100);">
                <div id="map" style="width: 100%; height: 100%; background: var(--gray-200);"></div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
<script>
    //Live location for interacive map
    document.addEventListener('DOMContentLoaded', function () {
        // 1) Data from PHP → JS
        window.listings = {{ Js::from(
            $properties->map(function ($p) {
                return [
                    'id'      => $p->id,
                    'title'   => $p->title,
                    'city'    => $p->city,
                    'address' => $p->address,
                    'lat'     => $p->latitude,
                    'lng'     => $p->longitude,
                    'rent'    => $p->monthly_rent,
                ];
            })->values()
        ) }};

        console.log('Listings inside script:', window.listings);

        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.warn('#map not found');
            return;
        }

        const map = L.map('map');

        // 2) Base layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markers = [];

        // 3) Property markers
        window.listings.forEach(listing => {
            if (!listing.lat || !listing.lng) return;

            const marker = L.marker([listing.lat, listing.lng]).addTo(map);
            marker.bindPopup(`
                <strong>${listing.title}</strong><br/>
                ${listing.city}<br/>
                LKR ${Number(listing.rent).toLocaleString()}
            `);
            markers.push(marker);
        });

        if (markers.length) {
            const group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.2));
        } else {
            map.setView([7.8731, 80.7718], 7); // Sri Lanka
        }

        // 4) USER LOCATION (debug version)
        if ('geolocation' in navigator) {
            console.log('Geolocation API available, requesting position...');

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    console.log('Geolocation SUCCESS:', position);

                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    // Make user marker visually different (circle)
                    const userMarker = L.circleMarker([userLat, userLng], {
                        radius: 8,
                        color: '#2563eb',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.9
                    }).addTo(map);

                    userMarker.bindPopup('You are here').openPopup();

                    markers.push(userMarker);
                    const group = L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.2));
                },
                function (error) {
                    console.warn('Geolocation ERROR:', error.code, error.message);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );
        } else {
            console.warn('Geolocation is not supported in this browser.');
        }
    });
</script>
@endpush




