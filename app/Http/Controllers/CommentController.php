<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Recipe;
use App\Notifications\CommentReplyNotification;
use App\Services\CommentVoteService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private CommentVoteService $commentVoteService
    ) {}

    public function store(StoreCommentRequest $request, Recipe $recipe)
    {
        $comment = $recipe->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->validated('content'),
            'parent_id' => $request->validated('parent_id'),
        ]);

        if ($comment->parent_id) {
            $parentComment = Comment::find($comment->parent_id);
            if ($parentComment && $parentComment->user_id !== Auth::id()) {
                $parentComment->user->notify(new CommentReplyNotification($comment));
            }
        }

        return back()->with('success', 'Commentaire ajouté');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Commentaire supprimé');
    }

    public function vote(Comment $comment, $type)
    {
        $this->commentVoteService->toggle($comment, Auth::id(), $type);

        return back();
    }
}
