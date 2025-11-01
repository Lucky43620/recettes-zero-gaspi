<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentVote;

class CommentVoteService
{
    public function toggle(Comment $comment, int $userId, string $type): void
    {
        $existingVote = CommentVote::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->first();

        if ($existingVote) {
            $this->handleExistingVote($comment, $existingVote, $type, $userId);
        } else {
            $this->createNewVote($comment, $userId, $type);
        }
    }

    private function handleExistingVote(Comment $comment, CommentVote $existingVote, string $newVote, int $userId): void
    {
        if ($existingVote->vote_type === $newVote) {
            $this->removeVote($comment, $existingVote, $userId);
        } else {
            $this->switchVote($comment, $existingVote, $newVote, $userId);
        }
    }

    private function removeVote(Comment $comment, CommentVote $existingVote, int $userId): void
    {
        if ($existingVote->vote_type === 'up') {
            $comment->decrement('upvotes');
        } else {
            $comment->decrement('downvotes');
        }

        CommentVote::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->delete();
    }

    private function switchVote(Comment $comment, CommentVote $existingVote, string $newVote, int $userId): void
    {
        if ($existingVote->vote_type === 'up') {
            $comment->decrement('upvotes');
            $comment->increment('downvotes');
        } else {
            $comment->increment('upvotes');
            $comment->decrement('downvotes');
        }

        CommentVote::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->update(['vote_type' => $newVote]);
    }

    private function createNewVote(Comment $comment, int $userId, string $vote): void
    {
        CommentVote::create([
            'user_id' => $userId,
            'comment_id' => $comment->id,
            'vote_type' => $vote,
        ]);

        if ($vote === 'up') {
            $comment->increment('upvotes');
        } else {
            $comment->increment('downvotes');
        }
    }
}
