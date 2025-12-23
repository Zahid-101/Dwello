@extends('layouts.dwello')

@section('title', $property->title . ' - Dwello')

@section('content')
<div class="container" style="padding: 32px 24px;">
    <div style="max-width: 900px; margin: 0 auto;">
        
        <div style="background: white; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 24px;">
            <div style="padding: 32px;">
                <div class="flex justify-between items-start" style="margin-bottom: 24px;">
                    <div>
                        <h1 style="font-size: 32px; font-weight: 700; color: var(--gray-900); margin-bottom: 8px;">{{ $property->title }}</h1>
                        <p style="color: var(--gray-600); font-size: 16px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:20px; height:20px; display:inline; vertical-align:text-bottom; margin-right:4px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            {{ $property->address }}, {{ $property->city }}
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 24px; font-weight: 700; color: var(--dwello-primary);">
                            Rs. {{ number_format($property->monthly_rent) }}
                            <span style="font-size: 14px; font-weight: 400; color: var(--gray-600);">/ month</span>
                        </div>
                        <div style="margin-top: 8px;">
                            <span class="badge" style="background: #EEF2FF; color: var(--dwello-primary);">{{ app(App\Http\Controllers\PropertyController::class)->getPropertyTypeName($property->property_type) ?? ucfirst($property->property_type) }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-2 gap-6" style="margin-bottom: 32px; border-top: 1px solid var(--gray-200); border-bottom: 1px solid var(--gray-200); padding: 24px 0;">
                    <div class="flex items-center" style="gap: 12px;">
                        <div style="background: var(--gray-100); padding: 12px; border-radius: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px; height:24px; color: var(--gray-700);">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <div>
                            <p style="color: var(--gray-500); font-size: 14px; margin:0;">Bedrooms</p>
                            <p style="font-weight: 600; color: var(--gray-900);">{{ $property->bedrooms }}</p>
                        </div>
                    </div>
                    <div class="flex items-center" style="gap: 12px;">
                        <div style="background: var(--gray-100); padding: 12px; border-radius: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px; height:24px; color: var(--gray-700);">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                        </div>
                        <div>
                            <p style="color: var(--gray-500); font-size: 14px; margin:0;">Bathrooms</p>
                            <p style="font-weight: 600; color: var(--gray-900);">{{ $property->bathrooms }}</p>
                        </div>
                    </div>
                    <div class="flex items-center" style="gap: 12px;">
                        <div style="background: var(--gray-100); padding: 12px; border-radius: 12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px; height:24px; color: var(--gray-700);">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0h9m-9 0a2.25 2.25 0 002.25 2.25h12a2.25 2.25 0 002.25-2.25m-9 0a2.25 2.25 0 00-2.25 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p style="color: var(--gray-500); font-size: 14px; margin:0;">Available From</p>
                            <p style="font-weight: 600; color: var(--gray-900);">{{ $property->available_from ? \Carbon\Carbon::parse($property->available_from)->format('M d, Y') : 'Now' }}</p>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 32px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px;">Description</h3>
                    <p style="color: var(--gray-700); line-height: 1.6;">
                        {{ $property->description ?? 'No description provided.' }}
                    </p>
                </div>

                @if($property->latitude && $property->longitude)
                <div style="margin-bottom: 32px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px;">Location</h3>
                    <div id="map" style="height: 350px; width: 100%; border-radius: 12px;"></div>
                </div>
                @endif
                
                <div class="flex justify-between items-center" style="margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--gray-200);">
                   <a href="{{ route('properties.index') }}" class="btn btn-outline">Back to Search</a>
                   {{-- If we had messaging --}}
                   {{-- <button class="btn btn-primary">Contact Landlord</button> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('map-scripts')
@if($property->latitude && $property->longitude)
<script>
document.addEventListener('DOMContentLoaded', function () {
    var lat = {{ $property->latitude }};
    var lng = {{ $property->longitude }};
    
    var map = L.map('map').setView([lat, lng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup("{{ $property->title }}")
        .openPopup();
});
</script>
@endif
@endpush
