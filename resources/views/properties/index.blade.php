{{-- resources/views/properties/index.blade.php --}}
@extends('layouts.dwello')

@section('title', 'Search Rooms - Dwello')

@push('styles')
    {{-- Leaflet CSS (free map library) --}}
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-VuZ8H6glCEI0CkU7tPZT+8pU02sCkWZ1CkMqP5pA9Po="
        crossorigin=""
    />
@endpush

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
                    <div class="flex items-center justify-between" style="margin-bottom: 24px;">
                        <h2 style="font-size: 20px; font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gray-900);">
                            {{ $properties->total() }} rooms found
                        </h2>
                        <select class="input">
                            <option>Best Match</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Newest First</option>
                        </select>
                    </div>

                    {{-- Listings --}}
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        @forelse ($properties as $property)
                            <div style="background: white; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); overflow: hidden;">
                                <div style="display: flex;">
                                    {{-- Image placeholder (later you can bind real image_url) --}}
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
                                            {{ Str::limit($property->description, 200) }}
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
    {{-- Leaflet JS --}}
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-o9N1j7kGAdzv2kXWr6yUAdJjYF3S9i5l+I5x3Fr2E0c="
        crossorigin="">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Default center (Colombo)
            const map = L.map('map').setView([6.9271, 79.8612], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const listings = @json(
                $properties->map(function ($p) {
                    return [
                        'id'    => $p->id,
                        'title' => $p->title,
                        'city'  => $p->city,
                        'rent'  => $p->monthly_rent,
                        'lat'   => $p->latitude,
                        'lng'   => $p->longitude,
                    ];
                })
            );

            const markers = [];

            listings.forEach(listing => {
                if (!listing.lat || !listing.lng) return;

                const marker = L.marker([listing.lat, listing.lng]).addTo(map);
                marker.bindPopup(
                    <strong>${listing.title}</strong><br> +
                    ${listing.city}<br> +
                    LKR ${listing.rent} / month
                );
                markers.push(marker);
            });

            if (markers.length > 0) {
                const group = L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.2));
            }
        });
    </script>
@endpush