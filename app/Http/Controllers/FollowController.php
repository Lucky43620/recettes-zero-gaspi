<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\FollowerNotification;
use App\Services\FollowService;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function __construct(
        private FollowService $followService
    ) {}

    public function follow(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas vous suivre vous-même']);
        }

        if ($this->followService->isFollowing(Auth::user(), $user)) {
            return back()->withErrors(['error' => 'Vous suivez déjà cet utilisateur']);
        }

        $this->followService->follow(Auth::user(), $user);
        $user->notify(new FollowerNotification(Auth::user()));

        return back()->with('success', 'Vous suivez maintenant ' . $user->name);
    }

    public function unfollow(User $user)
    {
        if (!$this->followService->isFollowing(Auth::user(), $user)) {
            return back()->withErrors(['error' => 'Vous ne suivez pas cet utilisateur']);
        }

        $this->followService->unfollow(Auth::user(), $user);

        return back()->with('success', 'Vous ne suivez plus ' . $user->name);
    }
}
