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

        $afterId = $request->input('after', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $afterId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get();

        $data = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'body' => $msg->body,
                'sender_id' => $msg->sender_id,
                'created_at' => $msg->created_at->toDateTimeString(),
                'sender_name' => $msg->sender->name,
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
