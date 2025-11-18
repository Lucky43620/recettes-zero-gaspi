<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invoiceId;
    protected $amount;

    public function __construct($invoiceId, $amount)
    {
        $this->invoiceId = $invoiceId;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Échec du paiement - Action requise')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Nous avons rencontré un problème lors du traitement de votre paiement.')
            ->line('Montant concerné : ' . number_format($this->amount / 100, 2) . ' €')
            ->line('Pour continuer à profiter de votre abonnement Premium, veuillez mettre à jour votre moyen de paiement.')
            ->action('Mettre à jour mon paiement', url('/subscription/payment-method'))
            ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'payment_failed',
            'invoice_id' => $this->invoiceId,
            'amount' => $this->amount,
            'message' => 'Votre paiement a échoué. Veuillez mettre à jour votre moyen de paiement.',
        ];
    }
}
