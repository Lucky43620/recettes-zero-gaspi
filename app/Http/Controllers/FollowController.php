<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas vous suivre vous-même']);
        }

        if (Auth::user()->isFollowing($user)) {
            return back()->withErrors(['error' => 'Vous suivez déjà cet utilisateur']);
        }

        Auth::user()->following()->attach($user->id);

        return back()->with('success', 'Vous suivez maintenant ' . $user->name);
    }

    public function unfollow(User $user)
    {
        if (!Auth::user()->isFollowing($user)) {
            return back()->withErrors(['error' => 'Vous ne suivez pas cet utilisateur']);
        }

        Auth::user()->following()->detach($user->id);

        return back()->with('success', 'Vous ne suivez plus ' . $user->name);
    }
}
