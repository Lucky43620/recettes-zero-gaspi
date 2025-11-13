<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $reply,
        public Comment $originalComment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Réponse à votre commentaire")
            ->line("{$this->reply->user->name} a répondu à votre commentaire.")
            ->line($this->reply->content)
            ->action('Voir la réponse', route('recipes.show', $this->originalComment->commentable))
            ->line('Merci d\'utiliser Recettes Zéro Gaspi !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'reply',
            'reply_id' => $this->reply->id,
            'comment_id' => $this->originalComment->id,
            'recipe_id' => $this->originalComment->commentable_id,
            'replier_id' => $this->reply->user_id,
            'replier_name' => $this->reply->user->name,
            'replier_avatar' => $this->reply->user->profile_photo_url,
            'content' => $this->reply->content,
            'url' => route('recipes.show', $this->originalComment->commentable),
        ];
    }
}
