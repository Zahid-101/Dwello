@extends('layouts.dwello')

@section('title', 'Favorites - Dwello')

@section('content')
<div class="container py-8">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Your Favorites</h1>
        <p class="text-gray-600">Profiles you have saved for later.</p>
    </div>

    @if($favorites->count() > 0)
        <div class="grid grid-3 gap-6">
            @foreach($favorites as $profile)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center relative">
                    <div style="position: absolute; top: 16px; right: 16px; cursor: pointer;" 
                         class="text-red-500"
                         onclick="event.preventDefault(); document.getElementById('remove-fav-{{ $profile->id }}').submit();">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <form id="remove-fav-{{ $profile->id }}" action="{{ route('favorites.toggle', $profile) }}" method="POST" style="display:none;">
                        @csrf
                    </form>

                    <div class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-bold mb-4">
                         {{ substr($profile->display_name ?? 'U', 0, 1) }}
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $profile->display_name }}</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ $profile->preferred_city }}</p>

                    <div class="grid grid-cols-2 gap-4 w-full bg-gray-50 p-3 rounded-xl mb-4">
                        <div>
                            <div class="font-bold text-blue-600">
                                @if($profile->budget_max)
                                    {{ number_format($profile->budget_max/1000) }}K
                                @else N/A @endif
                            </div>
                            <div class="text-xs text-gray-500">Budget</div>
                        </div>
                        <div>
                            <div class="font-bold text-blue-600">{{ $profile->age ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">Age</div>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full mt-auto">
                        <a href="{{ route('roommates.show', $profile) }}" class="flex-1 btn btn-outline py-2 text-sm justify-center">View</a>
                        <form action="{{ route('conversations.startRoommate', $profile->user_id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full btn btn-primary py-2 text-sm justify-center">Message</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="max-w-2xl mx-auto text-center py-12 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="w-16 h-16 bg-pink-50 text-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No favorites yet</h3>
            <p class="text-gray-500 mb-6">Start browsing profiles and tap the heart icon to save them here.</p>
            <a href="{{ route('roommates.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-white bg-blue-600 hover:bg-blue-700 transition">
                Browse Roommates
            </a>
        </div>
    @endif
</div>
@endsection
