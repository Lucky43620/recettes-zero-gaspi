<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCanceled extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Annulation de votre abonnement')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre abonnement Premium a été annulé.')
            ->line('Vous pourrez continuer à profiter des fonctionnalités premium jusqu\'à la fin de votre période de facturation.')
            ->action('Gérer mon abonnement', route('subscription.manage'))
            ->line('Nous espérons vous revoir bientôt !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscription_canceled',
            'message' => 'Votre abonnement Premium a été annulé.',
        ];
    }
}
