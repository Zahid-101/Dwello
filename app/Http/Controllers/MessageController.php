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

        if ($conversation->status === 'rejected') {
            abort(403, 'This conversation has been rejected.');
        }

        if ($conversation->status === 'pending') {
            // If sender IS the starter
            if ($conversation->started_by && auth()->id() == $conversation->started_by) {
                // Allow only if message count is 0 (just starting)
                // BUT wait... 'store' creates a message.
                // If the convo already has messages (count > 0), they can't send another.
                if ($conversation->messages()->count() > 0) {
                    // Check if last message was mine? No, simplest rule: 1 message until accepted.
                    // Actually, if I just started it, count might be 0.
                    // If I sent one, count is 1. I can't send sending.
                    return response()->json(['error' => 'Wait for the user to accept your request.'], 403);
                }
            } else {
                // Sender is NOT the starter (The Recipient)
                // Implicitly accept the conversation
                $conversation->update(['status' => 'accepted']);
            }
        }

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
            'property_id' => $message->property_id,
            'property' => $message->property ? [
                'id' => $message->property->id,
                'title' => $message->property->title,
                'monthly_rent' => $message->property->monthly_rent,
                'photo_url' => $message->property->photos->first() ? \Illuminate\Support\Facades\Storage::url($message->property->photos->first()->path) : null,
            ] : null,
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
            ->with(['sender', 'property.photos'])
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
                'property_id' => $msg->property_id,
                'property' => $msg->property ? [
                    'id' => $msg->property->id,
                    'title' => $msg->property->title,
                    'monthly_rent' => $msg->property->monthly_rent,
                    'photo_url' => $msg->property->photos->first() ? \Illuminate\Support\Facades\Storage::url($msg->property->photos->first()->path) : null,
                ] : null,
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
