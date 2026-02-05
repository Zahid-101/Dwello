<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendScheduledMessages extends Command
{
    protected $signature = 'messages:send-scheduled';
    protected $description = 'Send scheduled messages that are due';

    public function handle()
    {
        $dueMessages = \App\Models\ScheduledMessage::where('sent', false)
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($dueMessages as $scheduledMsg) {
            // Find or create conversation
            $userOneId = min($scheduledMsg->user_id, $scheduledMsg->receiver_id);
            $userTwoId = max($scheduledMsg->user_id, $scheduledMsg->receiver_id);

            // Check if conversation exists (generic check, ignoring type for now or defaulting to 'roommate' if unknown)
            // But ideally we should know the type. For simplicity, we'll try to find ANY conversation or create a 'roommate' one.
            $conversation = \App\Models\Conversation::where('user_one_id', $userOneId)
                ->where('user_two_id', $userTwoId)
                ->first();

            if (!$conversation) {
                $conversation = \App\Models\Conversation::create([
                    'type' => 'roommate', // Defaulting to roommate for new chats
                    'user_one_id' => $userOneId,
                    'user_two_id' => $userTwoId,
                    'status' => 'pending',
                    'started_by' => $scheduledMsg->user_id,
                    'last_message_at' => now(),
                ]);
            } else {
                $conversation->update(['last_message_at' => now()]);
            }

            // Create the message
            \App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $scheduledMsg->user_id,
                'body' => $scheduledMsg->message,
            ]);

            // Mark as sent
            $scheduledMsg->update(['sent' => true]);

            $this->info("Sent message ID: {$scheduledMsg->id}");
        }

        $this->info('All due messages processed.');
    }
}
