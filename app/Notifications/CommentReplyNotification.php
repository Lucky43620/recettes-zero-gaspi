<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $reply
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $recipe = $this->reply->recipe;
        $author = $this->reply->user;

        return (new MailMessage)
            ->subject('Nouvelle réponse à votre commentaire')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line($author->name . ' a répondu à votre commentaire sur la recette "' . $recipe->title . '".')
            ->line('"' . substr($this->reply->content, 0, 100) . (strlen($this->reply->content) > 100 ? '...' : '') . '"')
            ->action('Voir la recette', route('recipes.show', $recipe->slug))
            ->line('Merci d\'utiliser Recettes Zero Gaspi !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment_reply',
            'title' => 'Nouvelle réponse à votre commentaire',
            'message' => $this->reply->user->name . ' a répondu à votre commentaire',
            'comment_id' => $this->reply->id,
            'recipe_id' => $this->reply->recipe_id,
            'recipe_title' => $this->reply->recipe->title,
            'recipe_slug' => $this->reply->recipe->slug,
            'author_name' => $this->reply->user->name,
            'content_preview' => substr($this->reply->content, 0, 100),
            'action_url' => route('recipes.show', $this->reply->recipe->slug),
        ];
    }
}
