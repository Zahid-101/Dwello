<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduledMessageController extends Controller
{
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|array',
            'receiver_id.*' => 'exists:users,id',
            'message' => 'required|string|max:1000',
            'scheduled_at' => 'required|date|after:now',
        ]);

        foreach ($request->receiver_id as $receiverId) {
            \App\Models\ScheduledMessage::create([
                'user_id' => auth()->id(),
                'receiver_id' => $receiverId,
                'message' => $request->message,
                'scheduled_at' => $request->scheduled_at,
            ]);
        }

        return redirect()->back()->with('success', 'Messages scheduled successfully.');
    }
}
