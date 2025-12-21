<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    /**
     * Display a listing of conversations.
     */
    public function index()
    {
        $userId = auth()->id();

        $conversations = Conversation::forUser($userId)
            ->with(['property', 'userOne', 'userTwo', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('messages.index', compact('conversations'));
    }

    /**
     * Display the specified conversation (chat view).
     */
    public function show(Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);

        $conversation->load(['property', 'userOne', 'userTwo']);

        // Load recent messages
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc') // Oldest first for chat history
            ->get(); // In a real app we might paginate, but requirement says "load last 50" or similar. Limit if needed.
                     // The prompt suggested load last 50 oldest->newest. 
                     // Let's do a tailored query:
        
        $messages = $conversation->messages()
            ->with('sender')
            ->latest()
            ->take(50)
            ->get()
            ->sortBy('created_at');

        return view('messages.show', compact('conversation', 'messages'));
    }

    /**
     * Start a conversation about a property.
     */
    public function startProperty(Property $property)
    {
        $authUserId = auth()->id();
        $landlordId = $property->user_id;

        if ($authUserId == $landlordId) {
            return redirect()->back()->with('error', 'You cannot message yourself.');
        }

        $userOneId = min($authUserId, $landlordId);
        $userTwoId = max($authUserId, $landlordId);

        $conversation = Conversation::firstOrCreate(
            [
                'type' => 'property',
                'property_id' => $property->id,
                'user_one_id' => $userOneId,
                'user_two_id' => $userTwoId,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        return redirect()->route('messages.show', $conversation);
    }

    /**
     * Start a roommate conversation.
     */
    public function startRoommate(User $user)
    {
        $authUserId = auth()->id();
        $otherUserId = $user->id;

        if ($authUserId == $otherUserId) {
            return redirect()->back()->with('error', 'You cannot message yourself.');
        }

        $userOneId = min($authUserId, $otherUserId);
        $userTwoId = max($authUserId, $otherUserId);

        $conversation = Conversation::firstOrCreate(
            [
                'type' => 'roommate',
                'property_id' => null, // Explicitly match where property_id is null
                'user_one_id' => $userOneId,
                'user_two_id' => $userTwoId,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        return redirect()->route('messages.show', $conversation);
    }

    /**
     * Check if auth user is participant.
     */
    private function authorizeParticipant(Conversation $conversation)
    {
        $userId = auth()->id();
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403, 'Unauthorized action.');
        }
    }
}
