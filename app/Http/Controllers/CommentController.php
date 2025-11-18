<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Recipe;
use App\Notifications\CommentNotification;
use App\Notifications\CommentReplyNotification;
use App\Notifications\ReplyNotification;
use App\Services\CommentVoteService;
use App\Services\SettingsService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private CommentVoteService $commentVoteService,
        private SettingsService $settings
    ) {}

    public function store(StoreCommentRequest $request, Recipe $recipe)
    {
        // Vérifier si les commentaires sont activés
        if (!$this->settings->get('enable_comments', true)) {
            return back()->with('error', 'Les commentaires sont actuellement désactivés par l\'administrateur.');
        }

        $comment = $recipe->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->validated('content'),
            'parent_id' => $request->validated('parent_id'),
        ]);

        if ($comment->parent_id) {
            $comment->load('parent.user');
            if ($comment->parent && $comment->parent->user_id !== Auth::id()) {
                $comment->parent->user->notify(new ReplyNotification($comment, $comment->parent));
            }
        } else {
            if ($recipe->author_id !== Auth::id()) {
                $recipe->author->notify(new CommentNotification($comment, $recipe));
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
