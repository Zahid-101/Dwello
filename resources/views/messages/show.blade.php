{{-- resources/views/messages/show.blade.php --}}
@extends('layouts.dwello')

@section('title', 'Chat - Dwello')

@push('styles')
    <style>
        .chat-container { height: calc(100vh - 200px); min-height: 500px; display: flex; flex-direction: column; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 20px; background: #f9fafb; scroll-behavior: smooth; }
        .message-bubble { max-width: 70%; padding: 12px 16px; border-radius: 16px; position: relative; margin-bottom: 4px; word-wrap: break-word; }
        .message-bubble.mine { background: var(--dwello-primary, #3b82f6); color: white; align-self: flex-end; border-bottom-right-radius: 4px; margin-left: auto; }
        .message-bubble.theirs { background: white; color: #1f2937; align-self: flex-start; border-bottom-left-radius: 4px; border: 1px solid #e5e7eb; margin-right: auto; }
        .message-time { font-size: 11px; margin-bottom: 12px; opacity: 0.7; }
        .mine .message-time { text-align: right; color: rgba(255,255,255,0.8); }
        .theirs .message-time { text-align: left; color: #9ca3af; }
        .message-wrapper { display: flex; flex-direction: column; margin-bottom: 12px; }
    </style>
@endpush

@section('content')
@php
    $otherUser = $conversation->otherParticipant(auth()->id());
@endphp

<div class="container py-6">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden flex flex-col" style="height: calc(100vh - 140px);">
        <!-- Header -->
        <div class="p-4 border-b bg-white flex items-center justify-between shadow-sm z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('messages.index') }}" class="text-gray-500 hover:text-gray-700 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow">
                    {{ substr($otherUser->name ?? 'U', 0, 1) }}
                </div>
                
                <div>
                    <h2 class="font-bold text-gray-900 leading-tight">
                        {{ $otherUser->name ?? 'Unknown User' }}
                    </h2>
                    @if($conversation->type === 'property' && $conversation->property)
                        <a href="#" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            {{ Str::limit($conversation->property->title, 30) }}
                        </a>
                    @elseif($conversation->type === 'roommate')
                         <p class="text-xs text-gray-500">Roommate Chat</p>
                    @endif
                </div>
            </div>
            
            {{-- Optional actions menu could go here --}}
        </div>

        <!-- Chat Area -->
        <div id="chat-messages" class="chat-messages p-4 bg-gray-50">
            @foreach($messages as $message)
                @php $isMine = $message->sender_id == auth()->id(); @endphp
                <div class="message-wrapper {{ $isMine ? 'items-end' : 'items-start' }}" data-id="{{ $message->id }}">
                    <div class="message-bubble {{ $isMine ? 'mine' : 'theirs' }}">
                        {{ $message->body }}
                    </div>
                    <div class="message-time">
                        {{ $message->created_at->format('g:i A') }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t">
            <form id="chat-form" class="flex items-end gap-3" data-conversation-id="{{ $conversation->id }}">
                <div class="flex-1">
                    <textarea 
                        id="message-input" 
                        rows="1" 
                        class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 resize-none py-3 px-4 bg-gray-50 focus:bg-white transition" 
                        placeholder="Type a message..."
                        style="min-height: 48px; max-height: 120px;"
                    ></textarea>
                </div>
                <button type="submit" class="bg-blue-600 text-white rounded-xl p-3 hover:bg-blue-700 transition shadow-md flex items-center justify-center h-12 w-12 shrink-0">
                    <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.authUserId = {{ auth()->id() }};
    </script>
    <script src="{{ asset('js/chat.js') }}"></script>
    <script>
        // Auto-resize textarea
        const textarea = document.getElementById('message-input');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Scroll to bottom on load
        window.scrollToBottom = function() {
            const container = document.getElementById('chat-messages');
            container.scrollTop = container.scrollHeight;
        }
        window.addEventListener('load', window.scrollToBottom);
    </script>
@endpush

@endsection
