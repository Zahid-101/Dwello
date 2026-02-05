@extends('layouts.dwello')

@section('title', $property->title . ' - Dwello')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .property-header { position: relative; margin-bottom: 24px; }
    .gallery-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 12px; height: 400px; border-radius: 20px; overflow: hidden; margin-bottom: 32px; }
    .main-photo { height: 100%; width: 100%; object-fit: cover; }
    .side-photos { display: grid; gap: 12px; height: 100%; grid-template-rows: 1fr 1fr; }
    .side-photo { height: 100%; width: 100%; object-fit: cover; }
    .feature-card { background: var(--gray-50); padding: 16px; border-radius: 16px; text-align: center; }
    .feature-value { font-size: 18px; font-weight: 600; color: var(--gray-900); }
    .feature-label { font-size: 13px; color: var(--gray-600); }
    .map-container { height: 300px; width: 100%; border-radius: 20px; z-index: 1; }
    
    /* Star Rating */
    .star-rating { display: inline-flex; flex-direction: row-reverse; gap: 4px; }
    .star-rating input { display: none; }
    .star-rating label { font-size: 28px; color: #e5e7eb; cursor: pointer; transition: color 0.2s; }
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label { color: #f59e0b; }
    
    .review-section-container { background: white; border-radius: 24px; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid var(--gray-100); width: 50%; margin: 0 auto; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 md:px-6">
    
    {{-- Breadcrumb --}}
    <div style="margin-bottom: 24px; font-size: 14px; color: var(--gray-600);">
        <a href="{{ route('properties.index') }}" style="color: var(--dwello-primary); text-decoration: none;">Properties</a>
        <span style="margin: 0 8px;">/</span>
        {{ $property->city }}
        <span style="margin: 0 8px;">/</span>
        {{ Str::limit($property->title, 40) }}
    </div>

    {{-- Gallery --}}

    <div class="gallery-grid property-gallery-grid" style="{{ $property->photos->count() === 1 ? 'grid-template-columns: 1fr;' : '' }}">
        @if($property->photos->count() > 0)
            @php 
                $mainSrc = Storage::url($property->photos->first()->path);
            @endphp
            <img src="{{ $mainSrc }}" class="main-photo property-gallery-main" alt="{{ $property->title }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 16px;">
            
            @if($property->photos->count() > 1)
                <div class="side-photos property-gallery-side">
                    @php 
                        $src2 = Storage::url($property->photos[1]->path);
                    @endphp
                    <img src="{{ $src2 }}" class="side-photo" alt="Photo 2">
                    
                    @if($property->photos->count() > 2)
                        @php 
                            $src3 = Storage::url($property->photos[2]->path);
                        @endphp
                        <div style="position: relative; height: 100%;">
                            <img src="{{ $src3 }}" class="side-photo" alt="Photo 3">
                            @if($property->photos->count() > 3)
                                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); color: white; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 600; border-radius: 16px;">
                                    +{{ $property->photos->count() - 3 }} more
                                </div>
                            @endif
                        </div>
                    @else
                        <div style="background: var(--gray-100); height: 100%; border-radius: 16px;"></div>
                    @endif
                </div>
            @endif
        @else
            {{-- No photos placeholder --}}
            <div style="height: 400px; background: var(--gray-100); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: var(--gray-400);">
                No photos available
            </div>
        @endif
    </div>


    <div class="property-show-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; margin-bottom: 48px; align-items: start;">
        {{-- Left Content --}}
        <div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div>
                    <h1 class="text-2xl md:text-3xl font-poppins font-semibold text-gray-900 leading-tight mb-2">
                        {{ $property->title }}
                    </h1>
                    <p class="text-gray-600 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ $property->address }}, {{ $property->city }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-2xl md:text-3xl font-bold text-orange-500 whitespace-nowrap">
                        LKR {{ number_format($property->monthly_rent/1000, 1) }}k
                        <span class="text-base text-gray-500 font-normal">/mo</span>
                    </div>
                </div>
            </div>

            {{-- Key Feature Row --}}
            <div class="feature-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 32px;">
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
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-3 text-gray-900">About this property</h3>
                <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $property->description ?? 'No description provided.' }}
                </div>
            </div>

            {{-- Map --}}
            @if($property->latitude && $property->longitude)
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Location</h3>
                    <div id="map" class="map-container bg-gray-100 rounded-xl overflow-hidden"></div>
                </div>
            @endif
        </div>

        {{-- Right Sidebar --}}
        <div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <div class="flex items-center gap-3 mb-6">
                    @if($property->user && $property->user->profile_photo_url)
                         <img src="{{ $property->user->profile_photo_url }}" alt="{{ $property->user->name }}" class="w-14 h-14 rounded-full object-cover border border-orange-100 shadow-sm">
                    @else
                        <div class="w-14 h-14 rounded-full bg-orange-500 text-white flex items-center justify-center text-2xl font-boldshadow-sm">
                            {{ strtoupper(substr($property->user->name ?? 'L', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="font-semibold text-gray-900">{{ $property->user->name ?? 'Landlord' }}</div>
                        <div class="text-sm text-gray-500">Property Owner</div>
                        <a href="{{ route('users.show', $property->user) }}" class="text-sm font-medium text-dwello-primary hover:underline" style="color: var(--dwello-primary);">
                            View Profile
                        </a>
                    </div>
                </div>

                @auth
                    @if(auth()->id() !== $property->user_id)
                        <form action="{{ route('conversations.startProperty', $property) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="w-full bg-orange-500 text-white rounded-xl py-3 text-lg font-medium hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
                                Message Landlord
                            </button>
                        </form>

                        <button onclick="openShareModal()" class="w-full bg-white text-gray-700 border border-gray-300 rounded-xl py-3 text-lg font-medium hover:bg-gray-50 transition shadow-sm">
                            Share Property
                        </button>
                    @else
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('properties.edit', $property) }}" class="btn btn-outline w-full text-center">Edit Listing</a>
                            
                            <form action="{{ route('properties.destroy', $property) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this property? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-primary w-full" style="background-color: #EF4444; border-color: #EF4444;">Delete Listing</button>
                            </form>
                        </div>
                    @endif
                @else
                    <div style="text-align: center;">
                        <a href="{{ route('login') }}" class="w-full block text-center bg-orange-500 text-white rounded-xl py-3 text-lg font-medium hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
                            Log in to Message
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>
    </div>

    <!-- Share Modal -->
    <div id="share-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeShareModal()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                             <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Share Property</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">Search for a user to share "<strong>{{ $property->title }}</strong>" with.</p>
                                    
                                    <input type="text" id="user-search-input" placeholder="Search by name or email..." 
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    
                                    <div id="search-results" class="mt-4 max-h-60 overflow-y-auto space-y-2">
                                        <!-- Results will appear here -->
                                        <p class="text-sm text-gray-400 text-center py-2" id="empty-state">Start typing to search...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeShareModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function openShareModal() {
            document.getElementById('share-modal').classList.remove('hidden');
            document.getElementById('user-search-input').focus();
        }

        function closeShareModal() {
            document.getElementById('share-modal').classList.add('hidden');
            document.getElementById('user-search-input').value = '';
            document.getElementById('search-results').innerHTML = '<p class="text-sm text-gray-400 text-center py-2">Start typing to search...</p>';
        }

        // Search logic
        const input = document.getElementById('user-search-input');
        let timeout = null;

        input.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value;

            if (query.length < 2) {
                document.getElementById('search-results').innerHTML = '<p class="text-sm text-gray-400 text-center py-2">Start typing to search...</p>';
                return;
            }

            timeout = setTimeout(() => {
                fetch(`/users/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(users => {
                        const container = document.getElementById('search-results');
                        container.innerHTML = '';
                        
                        if (users.length === 0) {
                            container.innerHTML = '<p class="text-sm text-gray-400 text-center py-2">No users found.</p>';
                            return;
                        }

                        users.forEach(user => {
                            const div = document.createElement('div');
                            div.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition';
                            div.innerHTML = `
                                <div>
                                    <div class="font-medium text-gray-900">${user.name}</div>
                                    <div class="text-xs text-gray-500">${user.email}</div>
                                </div>
                                <button onclick="shareWithUser(${user.id})" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium hover:bg-blue-200">
                                    Send
                                </button>
                            `;
                            container.appendChild(div);
                        });
                    });
            }, 300);
        });

        // Share logic
        function shareWithUser(userId) {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Sending...';
            btn.disabled = true;

            fetch('{{ route("properties.share", $property) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ recipient_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    btn.innerHTML = 'Sent!';
                    btn.classList.remove('bg-blue-100', 'text-blue-700');
                    btn.classList.add('bg-green-100', 'text-green-700');
                    setTimeout(() => {
                        closeShareModal();
                         alert(data.message);
                    }, 500);
                } else {
                    alert(data.message);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error(error);
                alert('Something went wrong.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>

    {{-- Reviews Section --}}
    <div class="review-section-container mb-12">
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

        <div class="reviews-grid" style="display: grid; grid-template-columns: 1fr; gap: 32px; align-items: start;">
            {{-- Review List --}}
            <div>
                <div style="display: flex; align-items: baseline; gap: 12px; margin-bottom: 24px;">
                    <span style="font-size: 48px; font-weight: 700; color: var(--gray-900);">
                        {{ number_format($property->average_rating ?? 0, 1) }}
                    </span>
                    <span style="color: var(--gray-500);">/ 5.0 ({{ $property->approvedReviews->count() }} reviews)</span>
                </div>

                <div class="space-y-6" style="max-height: 600px; overflow-y: auto; padding-right: 16px; scrollbar-width: thin; scrollbar-color: var(--gray-300) transparent;">
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
                        <div class="text-center py-8 bg-gray-50 rounded-xl border border-gray-100">
                             <div class="inline-block p-3 rounded-full bg-gray-100 mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                             </div>
                             <p class="text-gray-500 font-medium">No reviews yet</p>
                             <p class="text-sm text-gray-400">Be the first to review this property!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Review Form --}}
            <div>
                @auth
                    @if(auth()->id() !== $property->user_id)
                        <div style="background: white; border: 1px solid var(--gray-200); padding: 24px; border-radius: 16px;">
                            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Write a Review</h3>
                            <form action="{{ route('reviews.store', $property) }}" method="POST">
                                @csrf
                                <div style="margin-bottom: 16px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Rating</label>
                                    <div class="star-rating">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{$i}}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                            <label for="star{{$i}}" title="{{ $i }} stars">★</label>
                                        @endfor
                                    </div>
                                    @error('rating')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div style="margin-bottom: 16px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Comment</label>
                                    <textarea name="comment" rows="4" class="@error('comment') border-red-500 @enderror" style="width: 100%; border: 1px solid var(--gray-300); border-radius: 8px; padding: 12px;">{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Review</button>
                            </form>
                        </div>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('share') === 'true') {
            openShareModal();
        }
    });
</script>
@endpush
