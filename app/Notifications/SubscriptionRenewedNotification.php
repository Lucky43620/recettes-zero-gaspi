<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $planName;
    protected $amount;
    protected $nextBillingDate;

    public function __construct($planName, $amount, $nextBillingDate)
    {
        $this->planName = $planName;
        $this->amount = $amount;
        $this->nextBillingDate = $nextBillingDate;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre abonnement a été renouvelé')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Votre abonnement Premium a été renouvelé avec succès.')
            ->line('Plan : ' . $this->planName)
            ->line('Montant : ' . number_format($this->amount / 100, 2) . ' €')
            ->line('Prochain prélèvement : ' . $this->nextBillingDate->format('d/m/Y'))
            ->action('Voir mon abonnement', url('/subscription/manage'))
            ->line('Merci de votre confiance !');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'subscription_renewed',
            'plan_name' => $this->planName,
            'amount' => $this->amount,
            'next_billing_date' => $this->nextBillingDate->toDateTimeString(),
            'message' => 'Votre abonnement ' . $this->planName . ' a été renouvelé.',
        ];
    }
}
