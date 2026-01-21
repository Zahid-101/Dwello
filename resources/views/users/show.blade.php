@extends('layouts.dwello')

@section('title', $user->name . ' - Profile')

@section('content')
<div class="container mx-auto px-4 py-8 md:px-6">
    <div style="margin-bottom: 24px;">
        <a href="{{ url()->previous() == url()->current() ? route('properties.index') : url()->previous() }}" class="btn btn-outline" style="border-radius: 12px; padding: 8px 16px;">
            &larr; Back
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
        {{-- Left Column: Profile Card --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 sticky top-24">
                <div class="flex flex-col items-center text-center">
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--dwello-primary); color:white; display:flex; align-items:center; justify-content:center; font-size:48px; overflow: hidden; margin-bottom: 16px;">
                        @if($user->profile_photo_path)
                            <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    
                    <h1 class="font-poppins font-bold text-gray-900 text-2xl mb-1">
                        {{ $user->name }}
                    </h1>
                    
                    <p class="text-gray-500 font-medium mb-6">
                        @if($user->isLandlord())
                            Property Owner
                        @elseif($user->isSeeker())
                            Roommate Seeker of {{ $user->name }}
                        @else
                            User
                        @endif
                    </p>

                    {{-- Actions --}}
                    @auth
                        @if(auth()->id() !== $user->id)
                            <form action="{{ route('conversations.startRoommate', $user->id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full bg-orange-500 text-white rounded-xl py-3 text-lg font-medium hover:bg-orange-600 transition shadow-lg shadow-orange-500/30 mb-4">
                                    Message {{ explode(' ', $user->name)[0] }}
                                </button>
                            </form>
                        @else
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline w-full mb-4">Edit Profile</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full block bg-orange-500 text-white rounded-xl py-3 text-lg font-medium hover:bg-orange-600 transition shadow-lg shadow-orange-500/30 mb-4">
                            Log in to Message
                        </a>
                    @endauth

                    <div class="w-full border-t border-gray-100 pt-6 mt-2 text-left">
                        <h3 class="font-semibold text-gray-900 mb-3">Contact Info</h3>
                        <ul class="space-y-3 text-sm text-gray-600">
                             <li class="flex items-center gap-2">
                                <span class="text-green-500">✔</span> {{ $user->email }}
                            </li>
                            @if($user->phone_number)
                                <li class="flex items-center gap-2">
                                    <span class="text-green-500">✔</span> {{ $user->phone_number }}
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Properties --}}
        <div class="md:col-span-2">
            <h2 class="text-2xl font-bold text-gray-900 font-poppins mb-6">
                Properties listed by {{ explode(' ', $user->name)[0] }}
            </h2>

            @if($user->properties->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach($user->properties as $property)
                        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition group">
                            <a href="{{ route('properties.show', $property) }}" class="block relative h-48 bg-gray-200 overflow-hidden">
                                @php
                                    $photo = $property->photos->first();
                                    $src = $photo ? Storage::url($photo->path) : null;
                                @endphp
                                @if($src)
                                    <img src="{{ $src }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        No Image
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-bold text-gray-900 shadow-sm">
                                    LKR {{ number_format($property->monthly_rent/1000, 1) }}k
                                </div>
                            </a>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 truncate mb-1">
                                    <a href="{{ route('properties.show', $property) }}" class="hover:underline">{{ $property->title }}</a>
                                </h3>
                                <p class="text-sm text-gray-500 flex items-center gap-1 mb-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $property->city }}
                                </p>
                                <div class="flex items-center gap-4 text-sm text-gray-600 border-t border-gray-50 pt-3">
                                    <span>{{ $property->bedrooms }} Bed</span>
                                    <span>{{ $property->bathrooms }} Bath</span>
                                    <span>{{ ucfirst($property->property_type) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 rounded-2xl p-8 text-center border border-gray-100">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">No active listings</p>
                    <p class="text-sm text-gray-400">This user hasn't posted any properties yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
