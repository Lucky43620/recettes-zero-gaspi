<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $daysRemaining;
    protected $endsAt;

    public function __construct($daysRemaining, $endsAt)
    {
        $this->daysRemaining = $daysRemaining;
        $this->endsAt = $endsAt;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre abonnement expire bientôt')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Votre abonnement Premium expire dans ' . $this->daysRemaining . ' jours.')
            ->line('Date d\'expiration : ' . $this->endsAt->format('d/m/Y'))
            ->line('Pour continuer à profiter de tous les avantages Premium, pensez à réactiver votre abonnement.')
            ->action('Réactiver mon abonnement', url('/subscription'))
            ->line('Merci de votre confiance !');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'subscription_expiring',
            'days_remaining' => $this->daysRemaining,
            'ends_at' => $this->endsAt->toDateTimeString(),
            'message' => 'Votre abonnement expire dans ' . $this->daysRemaining . ' jours.',
        ];
    }
}
