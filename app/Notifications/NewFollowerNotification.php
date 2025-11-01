<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewFollowerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $follower
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_follower',
            'title' => 'Nouveau abonné',
            'message' => $this->follower->name . ' vous suit maintenant',
            'follower_id' => $this->follower->id,
            'follower_name' => $this->follower->name,
            'action_url' => route('profile.show', $this->follower->id),
        ];
    }
}
