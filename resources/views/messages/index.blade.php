{{-- resources/views/messages/index.blade.php --}}
@extends('layouts.dwello')

@section('title', 'Messages - Dwello')

@section('content')
    <div class="container mx-auto px-4 py-8">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">Something went wrong.</span>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Messages</h1>
            <button onclick="openScheduleModal()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Schedule Message
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            @forelse($conversations as $conversation)
                @php
                    $otherUser = $conversation->otherParticipant(auth()->id());
                    $lastMessage = $conversation->messages->first();
                    // In controller eager load we did: 'messages' => fn($q) => $q->latest()->limit(1)
                @endphp
                <a href="{{ route('messages.show', $conversation) }}"
                    class="block p-4 border-b last:border-b-0 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            {{-- Avatar placeholder --}}
                            <div
                                class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shrink-0">
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
                                <p
                                    class="text-gray-600 text-sm truncate max-w-md {{ $conversation->unread_count > 0 ? 'font-semibold text-gray-900' : '' }}">
                                    @if($lastMessage)
                                        @if($lastMessage->sender_id == auth()->id())
                                            <span class="text-gray-400 font-normal">You:</span>
                                        @endif
                                        {{ $lastMessage->body }}
                                    @else
                                        <span class="italic text-gray-400 font-normal">No messages yet</span>
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

                            @if($conversation->unread_count > 0)
                                <div class="flex flex-col items-end gap-1">
                                    <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                        {{ $conversation->unread_count }}
                                    </span>
                                    <span class="text-blue-500 font-medium text-sm">Open</span>
                                </div>
                            @else
                                <span class="text-blue-500 font-medium text-sm">Open</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
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
        {{-- Schedule Message Modal --}}
        <div id="schedule-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4"
            style="display: none;">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden transform transition-all">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Schedule New Message</h3>
                    <button onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('messages.schedule') }}" method="POST" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Send To</label>

                        {{-- Custom Multi Select UI --}}
                        <div class="relative" id="multi-select-container">
                            <div class="w-full border border-gray-300 rounded-lg p-2 min-h-[42px] flex flex-wrap gap-2 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 bg-white"
                                onclick="document.getElementById('recipient-search').focus()">

                                <div id="selected-pills" class="flex flex-wrap gap-2"></div>
                                <input type="text" id="recipient-search" placeholder="Type to search..."
                                    class="flex-1 outline-none border-none p-1 text-sm min-w-[120px]"
                                    oninput="filterRecipients(this.value)" onfocus="showDropdown()" autocomplete="off">
                            </div>

                            {{-- Dropdown Options --}}
                            <div id="recipient-dropdown"
                                class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto hidden">
                                @foreach($recipients as $recipient)
                                    <div class="recipient-option p-2 hover:bg-blue-50 cursor-pointer flex items-center justify-between"
                                        data-id="{{ $recipient->id }}" data-name="{{ $recipient->name }}"
                                        onclick="toggleRecipient('{{ $recipient->id }}', '{{ $recipient->name }}')">
                                        <span>{{ $recipient->name }}</span>
                                        <svg class="w-4 h-4 text-blue-600 hidden check-icon" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @endforeach
                                <div id="no-results" class="p-2 text-gray-500 text-sm hidden">No users found</div>
                            </div>

                            {{-- Hidden Input for Form Submission --}}
                            <div id="hidden-inputs"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>
                            <input type="datetime-local" name="scheduled_at"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                min="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea name="message" rows="4"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Type your message here..." required></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeScheduleModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openScheduleModal() {
            document.getElementById('schedule-modal').style.display = 'flex';
        }
        function closeScheduleModal() {
            document.getElementById('schedule-modal').style.display = 'none';
        }

        // Multi-Select Logic
        let selectedRecipients = new Set();

        function showDropdown() {
            document.getElementById('recipient-dropdown').classList.remove('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const container = document.getElementById('multi-select-container');
            if (container && !container.contains(e.target)) {
                document.getElementById('recipient-dropdown').classList.add('hidden');
            }
        });

        function filterRecipients(query) {
            query = query.toLowerCase();
            let hasResults = false;
            document.querySelectorAll('.recipient-option').forEach(option => {
                const name = option.getAttribute('data-name').toLowerCase();
                if (name.includes(query)) {
                    option.style.display = 'flex';
                    hasResults = true;
                } else {
                    option.style.display = 'none';
                }
            });

            const noResults = document.getElementById('no-results');
            if (noResults) {
                noResults.style.display = hasResults ? 'none' : 'block';
            }
            showDropdown();
        }

        function toggleRecipient(id, name) {
            const searchInput = document.getElementById('recipient-search');

            if (selectedRecipients.has(id)) {
                removeRecipient(id);
            } else {
                addRecipient(id, name);
            }

            searchInput.value = '';
            filterRecipients('');
            searchInput.focus();
        }

        function addRecipient(id, name) {
            selectedRecipients.add(id);
            updateUI();
        }

        function removeRecipient(id) {
            selectedRecipients.delete(id);
            updateUI();
        }

        function updateUI() {
            // Update Pills
            const pillsContainer = document.getElementById('selected-pills');
            pillsContainer.innerHTML = '';

            selectedRecipients.forEach(id => {
                // Find name from DOM elements data attributes as fallback
                const option = document.querySelector(`.recipient-option[data-id="${id}"]`);
                const name = option ? option.getAttribute('data-name') : 'User';

                const pill = document.createElement('div');
                pill.className = 'bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded-full flex items-center gap-1';
                pill.innerHTML = `
                            ${name}
                            <button type="button" onclick="event.stopPropagation(); removeRecipient('${id}')" class="hover:text-blue-900 font-bold ml-1">×</button>
                        `;
                pillsContainer.appendChild(pill);
            });

            // Update Hidden Inputs
            const inputsContainer = document.getElementById('hidden-inputs');
            inputsContainer.innerHTML = '';
            selectedRecipients.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'receiver_id[]';
                input.value = id;
                inputsContainer.appendChild(input);
            });

            // Update Checkmarks in Dropdown
            document.querySelectorAll('.recipient-option').forEach(option => {
                const id = option.getAttribute('data-id');
                const check = option.querySelector('.check-icon');
                if (selectedRecipients.has(id)) {
                    check.classList.remove('hidden');
                    option.classList.add('bg-blue-50');
                } else {
                    check.classList.add('hidden');
                    option.classList.remove('bg-blue-50');
                }
            });
        }
    </script>
@endsection