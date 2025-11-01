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

        $newVote = $type === 'up' ? 1 : -1;

        if ($existingVote) {
            $this->handleExistingVote($comment, $existingVote, $newVote, $userId);
        } else {
            $this->createNewVote($comment, $userId, $newVote);
        }
    }

    private function handleExistingVote(Comment $comment, CommentVote $existingVote, int $newVote, int $userId): void
    {
        if ($existingVote->vote === $newVote) {
            $this->removeVote($comment, $existingVote, $userId);
        } else {
            $this->switchVote($comment, $existingVote, $newVote, $userId);
        }
    }

    private function removeVote(Comment $comment, CommentVote $existingVote, int $userId): void
    {
        if ($existingVote->vote === 1) {
            $comment->decrement('upvotes');
        } else {
            $comment->decrement('downvotes');
        }

        CommentVote::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->delete();
    }

    private function switchVote(Comment $comment, CommentVote $existingVote, int $newVote, int $userId): void
    {
        if ($existingVote->vote === 1) {
            $comment->decrement('upvotes');
            $comment->increment('downvotes');
        } else {
            $comment->increment('upvotes');
            $comment->decrement('downvotes');
        }

        CommentVote::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->update(['vote' => $newVote]);
    }

    private function createNewVote(Comment $comment, int $userId, int $vote): void
    {
        CommentVote::create([
            'user_id' => $userId,
            'comment_id' => $comment->id,
            'vote' => $vote,
        ]);

        if ($vote === 1) {
            $comment->increment('upvotes');
        } else {
            $comment->increment('downvotes');
        }
    }
}
