<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public float $amount,
        public string $currency
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $formattedAmount = number_format($this->amount, 2, ',', ' ') . ' ' . strtoupper($this->currency);

        return (new MailMessage)
            ->subject('Renouvellement de votre abonnement')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre abonnement Premium a été renouvelé avec succès.')
            ->line('Montant facturé : ' . $formattedAmount)
            ->action('Voir ma facture', route('subscription.manage'))
            ->line('Merci de votre confiance !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscription_renewed',
            'message' => 'Votre abonnement Premium a été renouvelé.',
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }
}
