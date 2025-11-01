<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class ExpiringItemsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Collection $expiringItems
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->expiringItems->count();
        $itemsList = $this->expiringItems->map(function ($item) {
            $daysLeft = $item->daysUntilExpiration();
            return "• {$item->ingredient->name} - Expire dans {$daysLeft} jour" . ($daysLeft > 1 ? 's' : '');
        })->implode("\n");

        return (new MailMessage)
            ->subject("⚠️ {$count} article" . ($count > 1 ? 's' : '') . " à consommer rapidement")
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line("Vous avez {$count} article" . ($count > 1 ? 's' : '') . " dans votre garde-manger qui expire" . ($count > 1 ? 'nt' : '') . " bientôt :")
            ->line($itemsList)
            ->action('Voir mon garde-manger', route('pantry.index'))
            ->line('Pensez à utiliser ces ingrédients pour éviter le gaspillage ! 🌱');
    }

    public function toArray(object $notifiable): array
    {
        $count = $this->expiringItems->count();

        return [
            'title' => "⚠️ Articles à consommer rapidement",
            'message' => "{$count} article" . ($count > 1 ? 's' : '') . " expire" . ($count > 1 ? 'nt' : '') . " bientôt dans votre garde-manger",
            'items' => $this->expiringItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->ingredient->name,
                    'days_left' => $item->daysUntilExpiration(),
                    'expiration_date' => $item->expiration_date?->format('Y-m-d'),
                ];
            })->toArray(),
            'action_url' => route('pantry.index'),
        ];
    }
}
