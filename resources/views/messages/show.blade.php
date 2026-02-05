{{-- resources/views/messages/show.blade.php --}}
@extends('layouts.dwello')

@section('title', 'Chat - Dwello')

@push('styles')
    <style>
        .chat-container {
            height: calc(100vh - 200px);
            min-height: 500px;
            display: flex;
            flex-direction: column;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f9fafb;
            scroll-behavior: smooth;
        }

        .message-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 16px;
            position: relative;
            margin-bottom: 4px;
            word-wrap: break-word;
        }

        .message-bubble.mine {
            background: var(--dwello-primary, #3b82f6);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            margin-left: auto;
        }

        .message-bubble.theirs {
            background: white;
            color: #1f2937;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            border: 1px solid #e5e7eb;
            margin-right: auto;
        }

        .message-time {
            font-size: 11px;
            margin-bottom: 12px;
            opacity: 0.7;
        }

        .mine .message-time {
            text-align: right;
            color: rgba(255, 255, 255, 0.8);
        }

        .theirs .message-time {
            text-align: left;
            color: #9ca3af;
        }

        .message-wrapper {
            display: flex;
            flex-direction: column;
            margin-bottom: 12px;
        }
    </style>
@endpush

@section('content')
    @php
        $otherUser = $conversation->otherParticipant(auth()->id());
    @endphp

    <div class="container mx-auto px-4 py-6 h-[calc(100vh-80px)]">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden flex flex-col"
            style="height: calc(100vh - 140px);">
            <!-- Header -->
            <div class="p-4 border-b bg-white flex items-center justify-between shadow-sm z-10">
                <div class="flex items-center gap-3">
                    <a href="{{ route('messages.index') }}" class="text-gray-500 hover:text-gray-700 p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>

                    <a href="{{ route('users.show', $otherUser) }}"
                        class="flex items-center gap-3 hover:opacity-80 transition">
                        @if($otherUser && $otherUser->profile_photo_url)
                            <img src="{{ $otherUser->profile_photo_url }}" alt="{{ $otherUser->name }}" class="w-10 h-10 rounded-full object-cover shadow">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow">
                                {{ substr($otherUser->name ?? 'U', 0, 1) }}
                            </div>
                        @endif

                        <div>
                            <h2 class="font-bold text-gray-900 leading-tight">
                                {{ $otherUser->name ?? 'Unknown User' }}
                            </h2>
                    </a>
                    @if($conversation->type === 'property' && $conversation->property)
                        <a href="#" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            {{ Str::limit($conversation->property->title, 30) }}
                        </a>
                    @elseif($conversation->type === 'roommate')
                        <p class="text-xs text-gray-500">Roommate Chat</p>
                    @endif
                </div>
            </div>

            <!-- Block Action -->
            @if($conversation->status !== 'rejected')
                <form action="{{ route('conversations.reject', $conversation) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to block this user? You will not be able to message each other.');">
                    @csrf
                    <button type="submit"
                        class="text-red-500 hover:text-red-700 font-medium text-sm flex items-center gap-1 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-full transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                            </path>
                        </svg>
                        Block User
                    </button>
                </form>
            @elseif($conversation->status === 'rejected' && $conversation->blocked_by == auth()->id())
                <form action="{{ route('conversations.unblock', $conversation) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-3 py-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-xs transition border border-gray-300">
                        Unblock User
                    </button>
                </form>
            @endif
        </div>

        <!-- Chat Area -->
        <div id="chat-messages" class="chat-messages p-4 bg-gray-50">
            @foreach($messages as $message)
                @php $isMine = $message->sender_id == auth()->id(); @endphp
                <div class="message-wrapper {{ $isMine ? 'items-end' : 'items-start' }}" data-id="{{ $message->id }}">
                    @if($message->property_id && $message->property)
                        <!-- Rich Property Card -->
                        <div class="message-bubble {{ $isMine ? 'mine' : 'theirs' }}"
                            style="padding: 0; overflow: hidden; max-width: 300px; background: white; color: black; border: 1px solid #e5e7eb;">
                            @php
                                $photo = $message->property->photos->first();
                                $src = $photo ? Storage::url($photo->path) : null;
                            @endphp
                            @if($src)
                                <img src="{{ $src }}" alt="Property" style="width: 100%; height: 150px; object-fit: cover;">
                            @else
                                <div
                                    style="width: 100%; height: 150px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    No Image
                                </div>
                            @endif

                            <div style="padding: 12px;">
                                <h4 style="font-weight: 600; font-size: 15px; margin-bottom: 4px; line-height: 1.3;">
                                    {{ Str::limit($message->property->title, 40) }}</h4>
                                <div style="color: #f97316; font-weight: 700; margin-bottom: 8px;">
                                    LKR {{ number_format($message->property->monthly_rent / 1000, 1) }}k <span
                                        style="font-weight: 400; color: #6b7280; font-size: 12px;">/mo</span>
                                </div>
                                <a href="{{ route('properties.show', $message->property) }}"
                                    class="block w-full text-center bg-gray-900 text-white rounded-lg py-2 text-sm font-medium hover:bg-gray-800 transition">
                                    View Property
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Standard Message -->
                        <div class="message-bubble {{ $isMine ? 'mine' : 'theirs' }}">
                            {{ $message->body }}
                        </div>
                    @endif
                    <div class="message-time">
                        {{ $message->created_at->format('g:i A') }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Input Area -->
        <!-- Input Area / Status Control -->
        <div class="p-4 bg-white border-t">
            @if($conversation->status === 'rejected')
                @if($conversation->blocked_by == auth()->id())
                    <div class="p-4 bg-gray-100 text-gray-600 rounded-lg text-center border border-gray-200">
                        <p class="font-medium">🚫 You blocked this user.</p>
                        <p class="text-sm mt-1">You cannot send messages to blocked users.</p>
                    </div>
                @else
                    <div class="p-4 bg-red-50 text-red-600 rounded-lg text-center border border-red-100">
                        <p class="font-medium">🚫 You have been blocked.</p>
                        <p class="text-sm mt-1">You cannot reply to this conversation.</p>
                    </div>
                @endif
            @elseif($conversation->status === 'pending')
                @php
                    $isStarter = $conversation->started_by == auth()->id();
                    $hasSentMessage = $conversation->messages()->where('sender_id', auth()->id())->exists();
                @endphp

                @if($isStarter)
                    @if($hasSentMessage)
                        <div class="p-4 bg-yellow-50 text-yellow-700 rounded-lg text-center border border-yellow-100">
                            <p class="font-medium">🔒 Request Sent</p>
                            <p class="text-sm mt-1">You can send more messages once {{ $otherUser->name }} accepts your request.</p>
                        </div>
                    @else
                        <!-- Limit Check for Free Users -->
                        @php
                            $weeklyLimitReached = false;
                            if (!auth()->user()->is_premium) {
                                $weeklyRequests = \App\Models\Conversation::where('started_by', auth()->id())
                                    ->where('created_at', '>=', now()->subDays(7))
                                    ->count();
                                // If they haven't sent this one yet (count=0 message), 
                                // this conversation itself counts as one of the requests if created recently?
                                // Actually, firstOrCreate touches timestamps. 
                                // Let's strictly say > 5 requests in last 7 days = Blocked.
                                if ($weeklyRequests > 5) {
                                    $weeklyLimitReached = true;
                                }
                            }
                        @endphp

                        @if($weeklyLimitReached)
                            <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center shadow-sm">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mb-3 text-red-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Limit Reached</h3>
                                <p class="text-gray-600 mb-4">Your message requests are full can send only after 1 week.</p>
                                <a href="{{ route('payment') }}" class="inline-block bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold py-2 px-6 rounded-lg hover:from-orange-600 hover:to-red-700 transition transform hover:scale-105 shadow-md">
                                    Pay Subscription
                                </a>
                            </div>
                        @else
                            <div class="mb-2 p-2 bg-blue-50 text-blue-700 rounded text-center text-sm border border-blue-100">
                                Send a message to start the request. They must accept to continue.
                            </div>
                            <form id="chat-form" class="flex items-end gap-3 no-loader" data-conversation-id="{{ $conversation->id }}">
                                <div class="flex-1">
                                    <textarea id="message-input" rows="1"
                                        class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 resize-none py-3 px-4 bg-gray-50 focus:bg-white transition"
                                        placeholder="Type your request..." style="min-height: 48px; max-height: 120px;"></textarea>
                                </div>
                                <button type="submit"
                                    class="bg-blue-600 text-white rounded-xl p-3 hover:bg-blue-700 transition shadow-md flex items-center justify-center h-12 w-12 shrink-0">
                                    <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    @endif
                @else
                    <!-- is Recipient -->
                    <div class="flex flex-col gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-center text-gray-700 font-medium">
                            {{ $otherUser->name }} wants to chat with you.
                        </p>
                        <p class="text-center text-gray-500 text-sm mb-2">
                            Accept to reply, or Reject to block.
                        </p>
                        <div class="flex gap-3 justify-center">
                            <form action="{{ route('conversations.accept', $conversation) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium shadow-sm transition">
                                    Accept
                                </button>
                            </form>
                            <form action="{{ route('conversations.reject', $conversation) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-white hover:bg-gray-50 text-red-600 border border-red-200 px-6 py-2 rounded-lg font-medium shadow-sm transition">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @else
                <!-- Normal Chat (Accepted) -->
                <form id="chat-form" class="flex items-end gap-3 no-loader" data-conversation-id="{{ $conversation->id }}">
                    <div class="flex-1">
                        <textarea id="message-input" rows="1"
                            class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 resize-none py-3 px-4 bg-gray-50 focus:bg-white transition"
                            placeholder="Type a message..." style="min-height: 48px; max-height: 120px;"></textarea>
                    </div>
                    <button type="submit"
                        class="bg-blue-600 text-white rounded-xl p-3 hover:bg-blue-700 transition shadow-md flex items-center justify-center h-12 w-12 shrink-0">
                        <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
    </div>

    @push('scripts')
        <script>
            window.authUserId = {{ auth()->id() }};
        </script>
        <script src="{{ asset('js/chat.js') }}?v={{ time() }}"></script>
        <script>
            // Auto-resize textarea
            const textarea = document.getElementById('message-input');
            textarea.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Scroll to bottom on load
            window.scrollToBottom = function () {
                const container = document.getElementById('chat-messages');
                container.scrollTop = container.scrollHeight;
            }
            window.addEventListener('load', window.scrollToBottom);
        </script>
    @endpush

@endsection