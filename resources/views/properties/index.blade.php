@extends('layouts.dwello')
@section('title', 'Search Rooms - Dwello')
@section('content')




    <!-- Main Content: Filters / Results / Map -->
    <div class="listing-layout" style="display: flex; flex-direction: row; min-height: calc(100vh - 160px);">
        {{-- Left pane: Filters --}}
        <div class="listing-sidebar" style="width: 320px; flex-shrink: 0; background: white; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); z-index: 10; padding: 24px; overflow-y: auto; border-right: 1px solid var(--gray-100);">
            <h2 style="font-size: 20px; font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gray-900); margin-bottom: 24px;">
                Filters
            </h2>

            <form method="GET" action="{{ route('properties.index') }}">
                {{-- Budget --}}
                <div style="margin-bottom: 24px;">
                    <h3 style="font-weight: 500; color: var(--gray-900); margin-bottom: 8px;">Budget (LKR)</h3>
                    <div class="flex" style="gap: 8px;">
                        <input class="input" type="number" step="100" min="0" name="min_rent" placeholder="Min"
                               value="{{ request('min_rent') }}" style="border-radius: 12px; width: 50%;">
                        <input class="input" type="number" step="100" min="0" name="max_rent" placeholder="Max"
                               value="{{ request('max_rent') }}" style="border-radius: 12px; width: 50%;">
                    </div>
                </div>

                {{-- City --}}
                <div style="margin-bottom: 24px;">
                    <h3 style="font-weight: 500; color: var(--gray-900); margin-bottom: 8px;">City</h3>
                    <select class="input" style="width:100%; border-radius:12px;" name="city">
                        <option value="">Any city</option>
                        @foreach(config('cities') as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Text search --}}
                <div style="margin-bottom: 24px;">
                    <h3 style="font-weight: 500; color: var(--gray-900); margin-bottom: 8px;">Search</h3>
                    <div style="position: relative;">
                        <input class="input" type="text" name="q" placeholder="Title, area, address"
                               value="{{ request('q') }}" style="border-radius: 12px; width: 100%; padding-right: 40px;">
                        <button type="submit" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--gray-500);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </button>
                    </div>
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

        {{-- Right pane: results + map --}}
        <div style="flex: 1; display: flex; flex-direction: row; position: relative;">
            {{-- Results --}}
            <div style="flex: 1; overflow-y: auto; padding: 24px; height: calc(100vh - 160px);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <h2 style="font-size: 20px; font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gray-900);">
                        {{ $properties->total() }} rooms found
                    </h2>
                </div>
                <p style="color: var(--gray-600); font-size: 14px; margin-bottom: 24px;">
                    Showing {{ $properties->count() }} result(s)
                    @if(request()->hasAny(['q','city','min_rent','max_rent','type']))
                        for your filters.
                    @endif
                </p>

                {{-- Listings --}}
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    @forelse ($properties as $property)
                        <div style="display: flex; flex-direction: row; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'" class="property-card-row">
                            {{-- Image placeholder --}}
                            <a href="{{ route('properties.show', $property) }}" style="width: 280px; height: 200px; position: relative; flex-shrink: 0; display: block;" class="property-card-img">
                                @if($property->photos->count() > 0)
                                    @php
                                        $src = Storage::url($property->photos->first()->path);
                                    @endphp
                                    <img src="{{ $src }}" alt="{{ $property->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=300&h=200&fit=crop" alt="{{ $property->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @endif
                            </a>

                            {{-- Content --}}
                            <div style="flex: 1; padding: 24px; display: flex; flex-direction: column;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                    <h3 style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 18px; color: var(--gray-900);">
                                        <a href="{{ route('properties.show', $property) }}" style="color: inherit; text-decoration: none;">
                                            {{ $property->title }}
                                        </a>
                                    </h3>
                                    <span style="font-size: 20px; font-weight: 700; color: var(--dwello-primary); white-space: nowrap; margin-left: 16px;">
                                        LKR {{ number_format($property->monthly_rent/1000, 1) }}k
                                        <span style="font-size: 13px; color: var(--gray-500); font-weight: 400;">/mo</span>
                                    </span>
                                </div>
                                <p style="color: var(--gray-600); font-size: 14px; margin-bottom: 12px; display: flex; align-items: center; gap: 4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    {{ $property->city }} • {{ $property->address }}
                                </p>
                                <p style="color: var(--gray-700); margin-bottom: 16px; flex-grow: 1; line-height: 1.5;">
                                    {{ \Illuminate\Support\Str::limit($property->description, 150) }}
                                </p>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto;">
                                    <div style="display: flex; gap: 8px;">
                                        <span class="badge badge-lifestyle">
                                            {{ ucfirst($property->property_type) }}
                                        </span>
                                        <span class="badge badge-lifestyle">
                                            {{ $property->bedrooms }} bed • {{ $property->bathrooms }} bath
                                        </span>
                                    </div>
                                    <a href="{{ route('properties.show', $property) }}" style="color: var(--dwello-primary); font-weight: 500; text-decoration: none;">
                                        View Details →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px; text-align: center;">
                            <div style="width: 64px; height: 64px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                                <svg style="width: 32px; height: 32px; color: var(--gray-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 style="font-size: 18px; font-weight: 600; color: var(--gray-900); margin-bottom: 8px;">No properties found</h3>
                            <p style="color: var(--gray-500); max-width: 350px; margin-bottom: 24px;">
                                We couldn't find any properties matching your criteria. Try adjusting your filters or search terms.
                            </p>
                            <a href="{{ route('properties.index') }}" class="btn btn-outline">
                                Clear all filters
                            </a>
                        </div>
                    @endforelse
                </div>

                <div style="margin-top: 24px;">
                    {{ $properties->links() }}
                </div>
            </div>

            {{-- Map --}}
            <div class="map-container" style="width: 40%; height: calc(100vh - 160px); background: var(--gray-100); position: sticky; top: 0;">
                <div id="map" style="width: 100%; height: 100%;"></div>
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
                    'url'     => route('properties.show', $p),
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
                LKR ${Number(listing.rent).toLocaleString()}<br/>
                <a href="${listing.url}" style="color: #F53003; font-weight: 500; text-decoration: none;">View Details</a>
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




