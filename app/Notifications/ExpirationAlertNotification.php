<?php

namespace App\Notifications;

use App\Models\PantryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpirationAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PantryItem $item
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysUntilExpiry = now()->diffInDays($this->item->expiry_date, false);

        $message = (new MailMessage)
            ->subject("Alerte péremption - {$this->item->name}")
            ->line("Votre {$this->item->name} expire bientôt !");

        if ($daysUntilExpiry == 0) {
            $message->line("⚠️ Ce produit expire aujourd'hui !");
        } elseif ($daysUntilExpiry == 1) {
            $message->line("⚠️ Ce produit expire demain !");
        } else {
            $message->line("⚠️ Ce produit expire dans {$daysUntilExpiry} jours.");
        }

        return $message
            ->action('Trouver une recette', route('recipes.index', ['ingredient' => $this->item->name]))
            ->line('Évitez le gaspillage alimentaire !');
    }

    public function toArray(object $notifiable): array
    {
        $daysUntilExpiry = now()->diffInDays($this->item->expiry_date, false);

        return [
            'type' => 'expiration',
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'quantity' => $this->item->quantity,
            'unit' => $this->item->unit,
            'expiry_date' => $this->item->expiry_date->format('Y-m-d'),
            'days_until_expiry' => $daysUntilExpiry,
            'url' => route('recipes.index', ['ingredient' => $this->item->name]),
        ];
    }
}
