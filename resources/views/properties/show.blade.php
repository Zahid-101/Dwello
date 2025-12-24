@extends('layouts.dwello')

@section('title', $property->title . ' - Dwello')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .property-header { position: relative; margin-bottom: 24px; }
    .gallery-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 12px; height: 400px; border-radius: 20px; overflow: hidden; margin-bottom: 32px; }
    .main-photo { height: 100%; width: 100%; object-fit: cover; }
    .side-photos { display: grid; grid-template-rows: 1fr 1fr; gap: 12px; height: 100%; }
    .side-photo { height: 100%; width: 100%; object-fit: cover; }
    .feature-card { background: var(--gray-50); padding: 16px; border-radius: 16px; text-align: center; }
    .feature-value { font-size: 18px; font-weight: 600; color: var(--gray-900); }
    .feature-label { font-size: 13px; color: var(--gray-600); }
    .map-container { height: 300px; width: 100%; border-radius: 20px; z-index: 1; }
    
    @media (max-width: 768px) {
        .gallery-grid { grid-template-columns: 1fr; height: auto; }
        .side-photos { display: none; }
        .main-photo { height: 300px; }
    }
</style>
@endpush

@section('content')
<div class="container" style="padding: 32px 24px;">
    
    {{-- Breadcrumb --}}
    <div style="margin-bottom: 24px; font-size: 14px; color: var(--gray-600);">
        <a href="{{ route('properties.index') }}" style="color: var(--dwello-primary); text-decoration: none;">Properties</a>
        <span style="margin: 0 8px;">/</span>
        {{ $property->city }}
        <span style="margin: 0 8px;">/</span>
        {{ Str::limit($property->title, 40) }}
    </div>

    {{-- Gallery --}}
    <div class="gallery-grid">
        @if($property->photos->count() > 0)
            <img src="{{ Storage::url($property->photos->first()->path) }}" class="main-photo" alt="{{ $property->title }}">
            <div class="side-photos">
                @if($property->photos->count() > 1)
                    <img src="{{ Storage::url($property->photos[1]->path) }}" class="side-photo" alt="Photo 2">
                @else
                    <div style="background: var(--gray-100); height: 100%;"></div>
                @endif
                
                @if($property->photos->count() > 2)
                    <div style="position: relative; height: 100%;">
                        <img src="{{ Storage::url($property->photos[2]->path) }}" class="side-photo" alt="Photo 3">
                        @if($property->photos->count() > 3)
                            <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); color: white; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 600;">
                                +{{ $property->photos->count() - 3 }} more
                            </div>
                        @endif
                    </div>
                @else
                    <div style="background: var(--gray-100); height: 100%;"></div>
                @endif
            </div>
        @else
            {{-- Fallback placeholder --}}
            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&h=600&fit=crop" class="main-photo" alt="Placeholder">
            <div class="side-photos">
                <div style="background: var(--gray-100);"></div>
                <div style="background: var(--gray-100);"></div>
            </div>
        @endif
    </div>

    <div class="grid grid-2 gap-8" style="margin-bottom: 48px; grid-template-columns: 2fr 1fr;">
        {{-- Left Content --}}
        <div>
            <div class="flex justify-between items-start" style="margin-bottom: 16px;">
                <div>
                    <h1 style="font-size: 32px; font-family: 'Poppins', sans-serif; font-weight: 600; color: var(--gray-900); line-height: 1.2; margin-bottom: 8px;">
                        {{ $property->title }}
                    </h1>
                    <p style="font-size: 16px; color: var(--gray-600); display: flex; align-items: center; gap: 6px;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ $property->address }}, {{ $property->city }}
                    </p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 32px; font-weight: 700; color: var(--dwello-primary);">
                        LKR {{ number_format($property->monthly_rent/1000, 1) }}k
                        <span style="font-size: 16px; color: var(--gray-500); font-weight: 400;">/mo</span>
                    </div>
                </div>
            </div>

            {{-- Key Feature Row --}}
            <div class="grid grid-4 gap-4" style="margin-bottom: 32px;">
                <div class="feature-card">
                    <div class="feature-value">{{ ucfirst($property->property_type) }}</div>
                    <div class="feature-label">Type</div>
                </div>
                <div class="feature-card">
                    <div class="feature-value">{{ $property->bedrooms }} Bed</div>
                    <div class="feature-label">Bedrooms</div>
                </div>
                <div class="feature-card">
                    <div class="feature-value">{{ $property->bathrooms }} Bath</div>
                    <div class="feature-label">Bathrooms</div>
                </div>
                <div class="feature-card">
                    <div class="feature-value">
                        @if($property->available_from)
                            {{ \Carbon\Carbon::parse($property->available_from)->format('M d') }}
                        @else
                            Now
                        @endif
                    </div>
                    <div class="feature-label">Availability</div>
                </div>
            </div>

            {{-- Description --}}
            <div style="margin-bottom: 32px;">
                <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 12px; color: var(--gray-900);">About this property</h3>
                <div style="color: var(--gray-700); line-height: 1.6; white-space: pre-line;">
                    {{ $property->description ?? 'No description provided.' }}
                </div>
            </div>

            {{-- Map --}}
            @if($property->latitude && $property->longitude)
                <div style="margin-bottom: 32px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 12px; color: var(--gray-900);">Location</h3>
                    <div id="map" class="map-container"></div>
                </div>
            @endif
        </div>

        {{-- Right Sidebar --}}
        <div>
            <div style="background: white; padding: 24px; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); position: sticky; top: 100px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--dwello-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        {{ strtoupper(substr($property->user->name ?? 'L', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--gray-900);">{{ $property->user->name ?? 'Landlord' }}</div>
                        <div style="font-size: 13px; color: var(--gray-500);">Property Owner</div>
                    </div>
                </div>

                @if(auth()->id() !== $property->user_id)
                    <form action="{{ route('conversations.startProperty', $property) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full" style="width: 100%; border-radius: 12px; padding: 14px; font-size: 16px;">
                            Message Landlord
                        </button>
                    </form>
                @else
                    <div style="text-align: center; color: var(--gray-500); padding: 12px; background: var(--gray-50); border-radius: 12px;">
                        This is your listing
                    </div>
                @endif
            </div>
        </div>
    </div>
        </div>
    </div>

    {{-- Reviews Section --}}
    <div style="margin-bottom: 48px; padding-top: 32px; border-top: 1px solid var(--gray-200);">
        <h2 style="font-size: 24px; font-weight: 600; color: var(--gray-900); margin-bottom: 24px;">Reviews</h2>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div style="background: #ecfdf5; color: #047857; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: #fef2f2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-2 gap-8" style="grid-template-columns: 1fr 1fr; gap: 48px;">
            {{-- Review List --}}
            <div>
                <div style="display: flex; align-items: baseline; gap: 12px; margin-bottom: 24px;">
                    <span style="font-size: 48px; font-weight: 700; color: var(--gray-900);">
                        {{ number_format($property->average_rating ?? 0, 1) }}
                    </span>
                    <span style="color: var(--gray-500);">/ 5.0 ({{ $property->approvedReviews->count() }} reviews)</span>
                </div>

                <div class="space-y-6">
                    @forelse($property->approvedReviews as $review)
                        <div style="background: var(--gray-50); padding: 16px; border-radius: 12px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <div style="font-weight: 600;">{{ $review->user->name }}</div>
                                <div style="color: var(--gray-500); font-size: 14px;">{{ $review->created_at->diffForHumans() }}</div>
                            </div>
                            <div style="color: #f59e0b; margin-bottom: 8px;">
                                @for($i = 0; $i < 5; $i++)
                                    @if($i < $review->rating) ★ @else ☆ @endif
                                @endfor
                            </div>
                            <p style="color: var(--gray-700);">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p style="color: var(--gray-500); font-style: italic;">No reviews yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Review Form --}}
            <div>
                @auth
                    @if(auth()->id() !== $property->user_id)
                        <?php
                            // Check conversation existence (simplified check in view for UI toggle, logic enforced in controller)
                            $hasConvo = \App\Models\Conversation::where('property_id', $property->id)
                                ->where(function($q) {
                                    $q->where('user_one_id', auth()->id())
                                      ->orWhere('user_two_id', auth()->id());
                                })->exists();
                        ?>

                        @if($hasConvo)
                            <div style="background: white; border: 1px solid var(--gray-200); padding: 24px; border-radius: 16px;">
                                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Write a Review</h3>
                                <form action="{{ route('reviews.store', $property) }}" method="POST">
                                    @csrf
                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Rating</label>
                                        <div style="display: flex; gap: 8px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <label style="cursor: pointer;">
                                                    <input type="radio" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required> {{ $i }}
                                                </label>
                                            @endfor
                                        </div>
                                    </div>

                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Comment</label>
                                        <textarea name="comment" rows="4" style="width: 100%; border: 1px solid var(--gray-300); border-radius: 8px; padding: 12px;">{{ old('comment') }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Review</button>
                                </form>
                            </div>
                        @else
                            <div style="background: var(--gray-50); padding: 24px; border-radius: 16px; text-align: center; color: var(--gray-600);">
                                <p>You verify this property by contacting the landlord before you can leave a review.</p>
                            </div>
                        @endif
                    @else
                        <div style="background: var(--gray-50); padding: 24px; border-radius: 16px; text-align: center; color: var(--gray-600);">
                            <p>You cannot review your own property.</p>
                        </div>
                    @endif
                @else
                    <div style="background: var(--gray-50); padding: 24px; border-radius: 16px; text-align: center; color: var(--gray-600);">
                        <p><a href="{{ route('login') }}" style="color: var(--dwello-primary);">Login</a> to leave a review.</p>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@if($property->latitude && $property->longitude)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([{{ $property->latitude }}, {{ $property->longitude }}], 14);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        L.marker([{{ $property->latitude }}, {{ $property->longitude }}]).addTo(map)
            .bindPopup("{{ $property->title }}")
            .openPopup();
    });
</script>
@endif
@endpush
