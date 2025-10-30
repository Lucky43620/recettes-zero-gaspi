<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\CommentVote;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Recipe $recipe)
    {
        $recipe->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->validated('content'),
            'parent_id' => $request->validated('parent_id'),
        ]);

        return back()->with('success', 'Commentaire ajouté');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Commentaire supprimé');
    }

    public function vote(Comment $comment, $type)
    {
        $userId = Auth::id();
        $existingVote = CommentVote::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->first();

        $newVote = $type === 'up' ? 1 : -1;

        if ($existingVote) {
            if ($existingVote->vote === $newVote) {
                if ($newVote === 1) {
                    $comment->decrement('upvotes');
                } else {
                    $comment->decrement('downvotes');
                }
                CommentVote::where('user_id', $userId)
                    ->where('comment_id', $comment->id)
                    ->delete();
            } else {
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
        } else {
            CommentVote::create([
                'user_id' => $userId,
                'comment_id' => $comment->id,
                'vote' => $newVote,
            ]);

            if ($newVote === 1) {
                $comment->increment('upvotes');
            } else {
                $comment->increment('downvotes');
            }
        }

        return back();
    }
}
