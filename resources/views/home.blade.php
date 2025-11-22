@extends('layouts.dwello')

@section('title', 'Dwello — Rooms & Flatmates in Sri Lanka')

@section('content')
<div style="background: var(--dwello-bg); padding: 40px 0 60px 0;">
    <div class="container" style="display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr); gap: 40px; align-items: center;">

        {{-- Left side: hero text --}}
        <div>
            <h2 style="font-family:'Poppins',sans-serif; font-size: 36px; font-weight:700; color:var(--gray-900); margin-bottom:12px;">
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
                    ✅ Only verified users can post listings
                </div>
                <div>
                    🔍 Filter by budget, city and lifestyle
                </div>
                <div>
                    📍 See everything on an interactive map
                </div>
            </div>
        </div>

        {{-- Right side: simple card stack --}}
        <div style="display:flex; flex-direction:column; gap:16px;">
            <div class="profile-card">
                <p style="font-size:13px; color:var(--gray-500); margin-bottom:4px;">Featured listing</p>
                <h3 style="font-family:'Poppins',sans-serif; font-size:18px; font-weight:600; margin-bottom:4px;">
                    Sunny room in Colombo 5
                </h3>
                <p style="font-size:14px; color:var(--gray-600); margin-bottom:8px;">
                    LKR 25,000 • Near Flower Road • Wi-Fi + water included
                </p>
                <p style="font-size:13px; color:var(--gray-500);">
                    “Looking for a clean and friendly flatmate. Ideal for APIIT / nearby uni students.”
                </p>
            </div>

            <div class="profile-card" style="background: var(--dwello-surface);">
                <p style="font-size:13px; color:var(--gray-600); margin-bottom:4px;">Flatmate highlight</p>
                <h3 style="font-family:'Poppins',sans-serif; font-size:18px; font-weight:600; margin-bottom:4px;">
                    Zahid · 22 · Student
                </h3>
                <p style="font-size:14px; color:var(--gray-700); margin-bottom:8px;">
                    Budget LKR 20k–30k · Colombo 5/6/7 · Non-smoker
                </p>
                <p style="font-size:13px; color:var(--gray-600);">
                    “Final-year IT student who likes quiet weekdays and chill weekends.”
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
