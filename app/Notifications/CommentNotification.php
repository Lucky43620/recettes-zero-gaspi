<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $comment,
        public Recipe $recipe
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nouveau commentaire sur {$this->recipe->title}")
            ->line("{$this->comment->user->name} a commenté votre recette {$this->recipe->title}.")
            ->line($this->comment->content)
            ->action('Voir le commentaire', route('recipes.show', $this->recipe))
            ->line('Merci d\'utiliser Recettes Zéro Gaspi !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment',
            'comment_id' => $this->comment->id,
            'recipe_id' => $this->recipe->id,
            'recipe_title' => $this->recipe->title,
            'commenter_id' => $this->comment->user_id,
            'commenter_name' => $this->comment->user->name,
            'commenter_avatar' => $this->comment->user->profile_photo_url,
            'content' => $this->comment->content,
            'url' => route('recipes.show', $this->recipe),
        ];
    }
}
