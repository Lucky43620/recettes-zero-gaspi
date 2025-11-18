<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public float $amount,
        public string $currency,
        public ?string $invoiceUrl = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $formattedAmount = number_format($this->amount, 2, ',', ' ') . ' ' . strtoupper($this->currency);

        $mail = (new MailMessage)
            ->error()
            ->subject('Échec du paiement de votre abonnement')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Le paiement de votre abonnement Premium a échoué.')
            ->line('Montant : ' . $formattedAmount)
            ->line('Veuillez mettre à jour votre moyen de paiement pour continuer à profiter de votre abonnement.');

        if ($this->invoiceUrl) {
            $mail->action('Payer maintenant', $this->invoiceUrl);
        } else {
            $mail->action('Mettre à jour mon moyen de paiement', route('subscription.manage'));
        }

        return $mail->line('Si vous avez des questions, n\'hésitez pas à nous contacter.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_failed',
            'message' => 'Le paiement de votre abonnement a échoué.',
            'amount' => $this->amount,
            'currency' => $this->currency,
            'invoice_url' => $this->invoiceUrl,
        ];
    }
}
