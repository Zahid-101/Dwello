<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRoommateMatch extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $profile;

    /**
     * Create a new notification instance.
     */
    public function __construct($profile)
    {
        $this->profile = $profile;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'profile_id' => $this->profile->id,
            'title' => 'New Roommate: ' . $this->profile->user->name,
            'message' => 'New roommate match in ' . $this->profile->preferred_city . ': ' . $this->profile->user->name,
            'link' => route('roommates.show', $this->profile->id),
        ];
    }
}
