@extends('layouts.dwello')

@section('title', 'Dwello — Rooms & Flatmates in Sri Lanka')

@section('content')
    <div style="background: var(--dwello-bg); padding: 40px 0 60px 0;">
        <div class="container home-grid-container"
            style="display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr); gap: 40px; align-items: center;">

            {{-- Left side: hero text --}}
            <div>
                <h2 class="home-hero-text"
                    style="font-family:'Poppins',sans-serif; font-size: 36px; font-weight:700; color:var(--gray-900); margin-bottom:12px;">
                    Find a room and a flatmate<br>you actually vibe with.
                </h2>
                <p style="font-size:16px; color:var(--gray-600); margin-bottom:24px; max-width:480px;">
                    Dwello helps young professionals and students in Sri Lanka find verified rooms
                    and compatible flatmates — no more sketchy ads or random Facebook groups.
                </p>

                <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
                    <a href="{{ route('properties.index') }}" class="btn btn-primary" style="border-radius: 16px;">
                        Browse rooms
                    </a>
                    <a href="{{ route('roommates.index') }}" class="btn btn-outline" style="border-radius: 16px;">
                        Find a flatmate
                    </a>
                </div>

                <div style="display:flex; gap:16px; flex-wrap:wrap; font-size:13px; color:var(--gray-600);">
                    <div>
                        🔍 Filter by budget, city and lifestyle
                    </div>
                    <div>
                        📍 See everything on an interactive map
                    </div>
                </div>
            </div>

            {{-- Right side: Boosted Ad --}}
            <div style="display:flex; flex-direction:column; gap:16px;">
                @if(isset($boostedAds) && $boostedAds->count() > 0)
                        <div id="boost-ad-carousel" style="position: relative; min-height: 200px;">
                            @foreach($boostedAds as $index => $ad)
                                    <div class="boost-ad-slide transition-opacity duration-1000 ease-in-out"
                                        style="display: {{ $index === 0 ? 'block' : 'none' }}; opacity: {{ $index === 0 ? '1' : '0' }}; transition: opacity 1s;">

                                        @if($ad->property)
                                            <a href="{{ route('properties.show', $ad->property_id) }}"
                                                class="block hover:opacity-95 transition-opacity" style="text-decoration: none;">
                                        @else
                                                <div class="block">
                                            @endif
                                                <div class="profile-card"
                                                    style="background: white; padding: 0; overflow: hidden; border: 2px solid #fcd34d;">
                                                    <div style="position: relative;">
                                                        @if($ad->property_image)
                                                            <img src="{{ Storage::url($ad->property_image) }}" alt="Featured Property"
                                                                style="width: 100%; height: 200px; object-fit: cover;">
                                                        @else
                                                            <div
                                                                style="width: 100%; height: 200px; background: linear-gradient(135deg, #fcd34d 0%, #f59e0b 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24px;">
                                                                Start Your Journey
                                                            </div>
                                                        @endif
                                                        <div
                                                            style="position: absolute; top: 12px; left: 12px; background: #fcd34d; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                            Featured Property
                                                        </div>
                                                    </div>
                                                    <div style="padding: 20px;">
                                                        <h3
                                                            style="font-family:'Poppins',sans-serif; font-size:18px; font-weight:600; margin-bottom:8px; display: flex; align-items: center; gap: 8px;">
                                                            Landlord Highlight
                                                            <span
                                                                style="font-size: 12px; font-weight: normal; color: #d97706; background: #fef3c7; padding: 2px 8px; border-radius: 12px;">Gold
                                                                Member</span>
                                                        </h3>
                                                        <p
                                                            style="font-size:14px; color:var(--gray-700); margin-bottom:16px; font-style: italic;">
                                                            "{{ $ad->note }}"
                                                        </p>

                                                        <div style="display: flex; align-items: center; gap: 12px;">
                                                            @if($ad->user->profile_photo_url)
                                                                <img src="{{ $ad->user->profile_photo_url }}" alt="{{ $ad->user->name }}"
                                                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                                            @else
                                                                <div
                                                                    style="width: 40px; height: 40px; border-radius: 50%; background: var(--dwello-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                                    {{ substr($ad->user->name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <div style="font-weight: 600; color: var(--gray-900);">{{ $ad->user->name }}
                                                                </div>
                                                                <div style="font-size: 12px; color: var(--gray-500);">Property Owner</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($ad->property)
                                                    </a>
                                                @else
                                            </div>
                                        @endif
                                </div>
                            @endforeach
                    </div>

                    @if($boostedAds->count() > 1)
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const slides = document.querySelectorAll('.boost-ad-slide');
                                let currentIndex = 0;
                                const intervalTime = 5000; // 5 seconds

                                setInterval(() => {
                                    const currentSlide = slides[currentIndex];
                                    const nextIndex = (currentIndex + 1) % slides.length;
                                    const nextSlide = slides[nextIndex];

                                    // Fade out current
                                    currentSlide.style.opacity = '0';
                                    setTimeout(() => {
                                        currentSlide.style.display = 'none';

                                        // Fade in next
                                        nextSlide.style.display = 'block';
                                        // Trigger reflow
                                        void nextSlide.offsetWidth;
                                        nextSlide.style.opacity = '1';
                                    }, 1000); // Wait for fade out to finish (matches css transition)

                                    currentIndex = nextIndex;
                                }, intervalTime);
                            });
                        </script>
                    @endif

                @else
                {{-- Default placeholder or empty if no boosted ad --}}
                <div class="profile-card"
                    style="display: flex; align-items: center; justify-content: center; height: 200px; background: #f9f9f9; color: #9ca3af;">
                    <div style="text-align: center;">
                        <p style="margin-bottom: 8px; font-weight: 500;">No featured property</p>
                        <a href="{{ route('boost.index') }}"
                            style="color: var(--dwello-primary); font-size: 13px; text-decoration: underline;">Boost your ad
                            here</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>
@endsection