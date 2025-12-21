{{-- resources/views/messages/index.blade.php --}}
@extends('layouts.dwello')

@section('title', 'Messages - Dwello')

@section('content')
<div class="container py-8">
    <h1 class="text-2xl font-bold mb-6">Messages</h1>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        @forelse($conversations as $conversation)
            @php
                $otherUser = $conversation->otherParticipant(auth()->id());
                $lastMessage = $conversation->messages->first(); 
                // In controller eager load we did: 'messages' => fn($q) => $q->latest()->limit(1)
            @endphp
            <a href="{{ route('messages.show', $conversation) }}" class="block p-4 border-b last:border-b-0 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        {{-- Avatar placeholder --}}
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shrink-0">
                            {{ substr($otherUser->name ?? 'U', 0, 1) }}
                        </div>

                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-semibold text-gray-900">{{ $otherUser->name ?? 'Unknown User' }}</h3>
                                @if($conversation->type === 'property' && $conversation->property)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                        Property: {{ Str::limit($conversation->property->title, 20) }}
                                    </span>
                                @elseif($conversation->type === 'roommate')
                                    <span class="text-xs bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full">
                                        Roommate Chat
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600 text-sm truncate max-w-md">
                                @if($lastMessage)
                                    @if($lastMessage->sender_id == auth()->id())
                                        <span class="text-gray-400">You:</span>
                                    @endif
                                    {{ $lastMessage->body }}
                                @else
                                    <span class="italic text-gray-400">No messages yet</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        @if($conversation->last_message_at)
                            <span class="text-xs text-gray-400 block mb-1">
                                {{ $conversation->last_message_at->diffForHumans() }}
                            </span>
                        @endif
                        <span class="text-blue-500 font-medium text-sm">Open</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p>No conversations yet.</p>
                <div class="mt-4 space-x-4">
                    <a href="{{ route('properties.index') }}" class="text-blue-500 hover:underline">Browse Properties</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('roommates.index') }}" class="text-blue-500 hover:underline">Find Roommates</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
