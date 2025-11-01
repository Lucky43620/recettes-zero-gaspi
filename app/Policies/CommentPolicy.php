<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function delete(User $user, Comment $comment): bool
    {
        $comment->loadMissing('recipe');

        return $user->id === $comment->user_id || $user->id === $comment->recipe->author_id;
    }
}
