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
            ->with([
                'property',
                'userOne',
                'userTwo',
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->withCount([
                'messages as unread_count' => function ($query) use ($userId) {
                    $query->where('sender_id', '!=', $userId)
                        ->whereNull('read_at');
                }
            ])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Fetch potential recipients (people the user has conversations with)
        $recipients = Conversation::forUser($userId)
            ->get()
            ->map(function ($conversation) use ($userId) {
                return $conversation->otherParticipant($userId);
            })
            ->filter()
            ->unique('id');

        return view('messages.index', compact('conversations', 'recipients'));
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


        // Mark unread messages as read
        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

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
                'status' => 'pending',
                'started_by' => $authUserId,
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
                'status' => 'pending',
                'started_by' => $authUserId,
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
    /**
     * Accept a message request.
     */
    public function accept(Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);

        if ($conversation->status !== 'pending') {
            return redirect()->back(); // Already handled
        }

        // Only the recipient (NOT the starter) can accept explicit requests
        // But implicit acceptance handled in MessageController. This is for the UI button.
        if ($conversation->started_by && auth()->id() == $conversation->started_by) {
            return redirect()->back()->with('error', 'You cannot accept your own request.');
        }

        $conversation->update(['status' => 'accepted']);

        return redirect()->route('messages.show', $conversation)->with('success', 'Request accepted.');
    }

    /**
     * Reject or Block a conversation.
     */
    public function reject(Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);

        // Update status to rejected and set who blocked it
        $conversation->update([
            'status' => 'rejected',
            'blocked_by' => auth()->id(),
        ]);

        return redirect()->route('messages.index')->with('success', 'User blocked.');
    }

    /**
     * Unblock a conversation.
     */
    public function unblock(Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);

        // Only the blocker can unblock
        if ($conversation->blocked_by != auth()->id()) {
            abort(403, 'You cannot unblock this user.');
        }

        $conversation->update([
            'status' => 'accepted',
            'blocked_by' => null,
        ]);

        return redirect()->back()->with('success', 'User unblocked.');
    }
}
