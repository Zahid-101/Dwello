<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Store a new message (AJAX).
     */
    public function store(Request $request, Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'body' => $request->body,
        ]);

        // Update conversation timestamp
        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'id' => $message->id,
            'body' => $message->body,
            'sender_id' => $message->sender_id,
            'created_at' => $message->created_at->toDateTimeString(),
            'sender_name' => auth()->user()->name, 
        ]);
    }

    /**
     * Poll for new messages (AJAX).
     */
    public function poll(Request $request, Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);

        $afterId = $request->input('after');
        $beforeId = $request->input('before');

        $query = $conversation->messages()
            ->with('sender');

        if ($afterId) {
            // Poll for new messages (Standard)
            // Get messages newer than X, oldest first (so they append naturally)
            $messages = $query->where('id', '>', $afterId)
                ->orderBy('created_at', 'asc')
                ->limit(50)
                ->get();
        } elseif ($beforeId) {
            // Load history
            // Get messages older than Y.
            // We want the closest ones to Y. So Order by Created DESC, limit 50.
            // Then reverse them to return in chronological order for the JS to handle?
            // Or just return them. 
            // JS iterates backwards to prepend. 
            // If we return [Oldest, ..., Newest-Before-Y]
            // JS loop: Prepend Newest-Before-Y (Top), Prepend ... (Top), Prepend Oldest (Top).
            // Result: [Oldest, ..., Newest-Before-Y] at top. Correct.
            
            // So we need [Msg 1... Msg 50] sorted ASC.
            // But to get the *previous* 50, we need to order by DESC to find them, then sort back ASC.
            
            $messages = $query->where('id', '<', $beforeId)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->sortBy('created_at')
                ->values();
        } else {
            // Fallback (shouldn't really hit this via poll, but maybe initial load?)
            $messages = collect([]); 
        }

        $data = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'body' => $msg->body,
                'sender_id' => $msg->sender_id,
                'created_at' => $msg->created_at->toDateTimeString(),
                'sender_name' => $msg->sender ? $msg->sender->name : 'Unknown',
            ];
        });

        return response()->json($data);
    }

    /**
     * Check authorization.
     */
    private function authorizeParticipant(Conversation $conversation)
    {
        $userId = auth()->id();
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403, 'Unauthorized action.');
        }
    }
}
