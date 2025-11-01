<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Recipe;
use App\Services\CommentVoteService;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct(
        private CommentVoteService $commentVoteService
    ) {}

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
        $this->commentVoteService->toggle($comment, Auth::id(), $type);

        return back();
    }
}
