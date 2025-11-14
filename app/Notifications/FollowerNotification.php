<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $follower
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nouveau follower")
            ->line("{$this->follower->name} vous suit désormais !")
            ->action('Voir son profil', route('user.profile', $this->follower))
            ->line('Merci d\'utiliser Recettes Zéro Gaspi !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'follower',
            'follower_id' => $this->follower->id,
            'follower_name' => $this->follower->name,
            'follower_avatar' => $this->follower->profile_photo_url,
            'url' => route('user.profile', $this->follower),
        ];
    }
}
