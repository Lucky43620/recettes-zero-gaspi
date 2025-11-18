<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $endsAt;

    public function __construct($endsAt)
    {
        $this->endsAt = $endsAt;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre abonnement a été annulé')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Votre abonnement Premium a été annulé.')
            ->line('Vous pourrez continuer à profiter de vos avantages jusqu\'au : ' . $this->endsAt->format('d/m/Y'))
            ->line('Après cette date, votre compte reviendra au plan gratuit.')
            ->action('Réactiver mon abonnement', url('/subscription'))
            ->line('Nous espérons vous revoir bientôt !');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'subscription_cancelled',
            'ends_at' => $this->endsAt->toDateTimeString(),
            'message' => 'Votre abonnement a été annulé et prendra fin le ' . $this->endsAt->format('d/m/Y'),
        ];
    }
}
